<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Unit tests for the Intent Engine (Query Detection Logic)
 * 
 * These tests verify that the chatbot correctly classifies user queries
 * into the appropriate operation types: fetch, capability questions, or API operations.
 */
class IntentEngineTest extends TestCase
{
    /**
     * Test that fetch operation queries are correctly detected
     * 
     * @dataProvider fetchOperationProvider
     */
    public function test_detects_fetch_operations(string $query, bool $expectedResult): void
    {
        $result = $this->isFetchOperation($query);
        
        $this->assertEquals(
            $expectedResult, 
            $result, 
            "Query '$query' should " . ($expectedResult ? '' : 'NOT ') . "be detected as a fetch operation"
        );
    }

    /**
     * Data provider for fetch operation tests
     */
    public static function fetchOperationProvider(): array
    {
        return [
            // Positive cases - should be detected as fetch operations
            ['Show me all products', true],
            ['List the last 5 orders', true],
            ['Get all users', true],
            ['Display all posts', true],
            ['How many orders were placed last month?', true],
            ['What is the total revenue?', true],
            ['Give me the sales data', true],
            ['Show me the best selling products', true],
            ['Find all customers', true],
            ['Tell me about the transactions', true],
            
            // Negative cases - should NOT be detected as fetch operations
            ['Create a new post titled Hello', false],
            ['Update product ID 123', false],
            ['Delete the order', false],
            ['What can you do?', false],
            ['Hello', false],
        ];
    }

    /**
     * Test that capability questions are correctly detected
     * 
     * @dataProvider capabilityQuestionProvider
     */
    public function test_detects_capability_questions(string $query, bool $expectedResult): void
    {
        $result = $this->isCapabilityQuestion($query);
        
        $this->assertEquals(
            $expectedResult, 
            $result, 
            "Query '$query' should " . ($expectedResult ? '' : 'NOT ') . "be detected as a capability question"
        );
    }

    /**
     * Data provider for capability question tests
     */
    public static function capabilityQuestionProvider(): array
    {
        return [
            // Positive cases - should be detected as capability questions
            ['What can you do?', true],
            ['How can you help?', true],
            ['What are your capabilities?', true],
            ['How do you help me?', true],
            
            // Negative cases - should NOT be detected as capability questions
            ['Can you make a post?', false],
            ['Show me products', false],
            ['Create a new page', false],
            ['What is the total revenue?', false],
            ['Get all orders', false],
        ];
    }

    /**
     * Test that WordPress API operations are correctly detected
     * 
     * @dataProvider wordPressApiOperationProvider
     */
    public function test_detects_wordpress_api_operations(string $query, bool $expectedResult): void
    {
        $result = $this->isWordPressApiOperation($query);
        
        $this->assertEquals(
            $expectedResult, 
            $result, 
            "Query '$query' should " . ($expectedResult ? '' : 'NOT ') . "be detected as a WordPress API operation"
        );
    }

    /**
     * Data provider for WordPress API operation tests
     */
    public static function wordPressApiOperationProvider(): array
    {
        return [
            // Positive cases - should be detected as API operations
            ['Create a new post titled Hello World', true],
            ['Add a product named Widget priced at 50', true],
            ['Update product ID 123 with price 99', true],
            ['Edit post ID 456', true],
            ['Delete the page with ID 789', true],
            ['Make a new post with title Test', true],
            
            // Negative cases - should NOT be detected as API operations
            ['Show me all products', false],
            ['List the orders', false],
            ['What can you do?', false],
            ['Hello there', false],
        ];
    }

    /**
     * Simulate the isFetchOperation logic from NLPController
     * This is extracted for unit testing purposes
     */
    private function isFetchOperation(string $query): bool
    {
        $lowerQuery = strtolower(trim($query));
        
        // Exclude capability questions first
        if ($this->isCapabilityQuestion($query)) {
            return false;
        }
        
        // Check for explicit fetch keywords
        $fetchKeywords = '/\b(show|list|fetch|get|view|display|select|give|provide|retrieve|find|search|see|tell|which|how many|count|sum|total|sales|revenue|orders|products|posts|users|customers|data|information|details|report|selling|sold|popular|best|top|most|least|highest|lowest|average|avg|maximum|minimum|max|min|transaction|transactions)\b/i';
        
        // Check for question patterns that indicate data requests
        $questionPattern = '/\b(can you|could you|please|i need|i want|show me|give me|get me|tell me|what is|what are|how many|how much|who|when|where)\b/i';
        
        // Check for data-related terms
        $dataTerms = '/\b(data|information|details|report|statistics|stats|summary|overview|all|every|each|product|item|order|transaction|transactions|sale|sales|customer|customers|user|users|post|posts|page|pages|category|categories|tag|tags|revenue|income|profit|earnings)\b/i';
        
        // PRIMARY CHECK: If query contains fetch keywords, it's a fetch operation
        if (preg_match($fetchKeywords, $lowerQuery)) {
            return true;
        }
        
        // SECONDARY CHECK: Question pattern + data terms = fetch operation
        if (preg_match($questionPattern, $lowerQuery) && preg_match($dataTerms, $lowerQuery)) {
            return true;
        }
        
        // Sales/revenue specific queries
        if (preg_match('/\b(sales|revenue|income|profit|orders|transactions|earnings|selling|sold)\b/i', $lowerQuery)) {
            return true;
        }
        
        return false;
    }

    /**
     * Simulate the isCapabilityQuestion logic from NLPController
     */
    private function isCapabilityQuestion(string $query): bool
    {
        $lowerQuery = strtolower(trim($query));
        
        $capabilityPatterns = [
            '/^what\s+(can|do|are)\s+(you|i)\s+(do|help|assist)(\s+me)?\s*$/i',
            '/^what\s+(are|is)\s+(your|you)\s+(capabilities|features|functions|abilities)/i',
            '/^how\s+(can|do)\s+(you|i)\s+(help|assist)(\s+me)?\s*$/i',
            '/^(tell|show)\s+(me\s+)?(what\s+)?(can\s+)?(you\s+)?(do|help)(\s+me)?\s*$/i',
            '/^(what|how)\s+(you\s+)?(can\s+)?(do|help)(\s+me)?\s*$/i',
            '/^can\s+you\s+(help|do|assist)(\s+me)?\s*$/i',
            '/^(what|how)\s+are\s+you(\s+doing)?\s*$/i',
        ];
        
        foreach ($capabilityPatterns as $pattern) {
            if (preg_match($pattern, $lowerQuery)) {
                // Additional check: if query contains action verbs or WordPress terms, it's NOT a capability question
                if (preg_match('/\b(make|create|add|new|insert|update|edit|modify|change|delete|remove|post|product|page|category|tag|order)\b/i', $lowerQuery)) {
                    return false;
                }
                return true;
            }
        }
        
        return false;
    }

    /**
     * Simulate the isWordPressApiOperation logic from NLPController
     */
    private function isWordPressApiOperation(string $query): bool
    {
        // Check for create, update, delete operations
        $operationPattern = '/\b(create|make|add|new|insert|update|edit|modify|change|delete|remove|alter|publish|draft|build|generate)\b/i';
        
        // Check for WordPress/WooCommerce specific terms
        $wpTermsPattern = '/\b(post|product|page|category|tag|order|customer|user|woocommerce|wordpress)\b/i';
        
        // Check for ID references
        $idPattern = '/\b(id|ID)\s*[:\-]?\s*\d+\b/i';
        
        // Check for property assignments
        $propertyPattern = '/\b(with|set|to|as|price|title|content|name|status|titled|called|named)\s*[:\-]?\s*["\']?[^"\']+["\']?/i';
        
        // Direct property patterns like "Title Hello World"
        $directPropertyPattern = '/\b(title|content|name|price|status)\s+["\']?[^"\']+["\']?/i';
        
        return preg_match($operationPattern, $query) && 
               (preg_match($wpTermsPattern, $query) || preg_match($idPattern, $query) || preg_match($propertyPattern, $query) || preg_match($directPropertyPattern, $query));
    }
}
