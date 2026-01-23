---
title: 'HeyTrisha: An Extensible Natural Language Interface to Database (NLIDB) Framework for E-Commerce Platforms'
tags:
  - PHP
  - JavaScript
  - WordPress
  - WooCommerce
  - natural language processing
  - conversational analytics
  - e-commerce
  - NLIDB
authors:
  - name: Manikandan Chandran
    orcid: 0009-0006-5776-8856
    corresponding: true
    affiliation: 1
affiliations:
  - name: Kalaignar Karunanithi Institute of Technology, Coimbatore, Senior Software Engineer, Phoenix, Arizona, United States
    index: 1
date: 22 January 2026
bibliography: paper.bib
---

# Summary

HeyTrisha is a conversational analytics engine that enables administrators to query e-commerce databases using natural language. Instead of writing SQL queries or navigating complex dashboards, users can ask everyday business questions like "how well sales have been doing lately" or "how many new customers joined last week" and receive instant, accurate responses.

E-commerce platforms generate voluminous operational data, yet extracting insights typically requires technical proficiency in Structured Query Language (SQL) or reliance on rigid, pre-defined dashboards. HeyTrisha bridges the gap between unstructured natural language queries and structured database operations through a modular architecture that separates Natural Language Understanding (NLU) from platform-specific data execution.

The software utilizes a decoupled Adapter Pattern architecture, allowing it to be extended to various e-commerce platforms while maintaining a consistent query interface. The reference implementation for WooCommerce demonstrates how disparate database schemas can be normalized into a unified query interface, making it an ideal testbed for researchers studying Human-Computer Interaction (HCI) and Natural Language Interfaces to Databases (NLIDB).

# Statement of need

The rapid expansion of e-commerce platforms has led to an explosion of structured operational data. However, a significant "technical barrier" remains for non-technical administrators and researchers who wish to perform ad-hoc analysis. Current solutions generally fall into two categories: static dashboards, which lack flexibility, or direct database access, which requires SQL expertise [@Androutsopoulos:1995].

This dichotomy presents a specific challenge in the field of Human-Data Interaction (HDI): how to translate informal human intent into rigid database logic without compromising security or system stability. While generic Large Language Model (LLM) tools exist, they often suffer from "hallucinations" when dealing with complex, proprietary schemas like those found in WordPress/WooCommerce [@OpenAI:2023].

HeyTrisha addresses this scientific gap by abstracting data access through a strictly typed Adapter interface. The software allows researchers to experiment with Natural Language Interfaces to Databases in a real-world commercial environment [@Li:2014; @Popescu:2003]. It provides a standardized framework for translating vague human concepts (e.g., "how are we doing?") into precise, parameterized queries, significantly lowering the barrier for operational analytics research.

# Software Architecture

HeyTrisha implements a modular, n-tier architecture designed to ensure platform independence. The system is composed of three primary layers:

**The Core (Intent Engine)**: This layer acts as the semantic parser. It accepts natural language input and utilizes probabilistic intent classification to map the user's request to a normalized JSON Intermediate Representation (IR). This layer is agnostic of the underlying database structure.

**The Adapter Interface**: A strict contract (Interface) that defines necessary analytics primitives (e.g., `fetch_orders`, `get_customer_cohorts`). This ensures that the Core can communicate with any data source that implements the interface.

**The Execution Layer**: The reference implementation specifically maps the JSON IR to the WordPress database schema (`wp_posts`, `wp_postmeta`), handling the complexities of EAV (Entity-Attribute-Value) storage models common in CMS platforms.

![HeyTrisha Data Flow Architecture. The process begins with natural language input, passes through semantic parsing and security validation, then routes through the Adapter Interface to platform-specific execution.\label{fig:architecture}](assets/img/heytrisha.jpeg)

# Key Functionalities

The software provides the following key functionalities:

- **Zero-Shot Query Translation**: The system maps natural language to database queries without requiring pre-training on the specific store's dataset.

- **Temporal Resolution**: It includes a logic layer for resolving relative time expressions (e.g., "last quarter," "year to date") into precise SQL DATETIME boundaries.

- **Security-First Execution**: Unlike "text-to-SQL" generators that can pose security risks, HeyTrisha uses a Parameter Binding approach. The NLU layer identifies parameters, which are then passed to pre-validated, secure SQL queries within the adapter, preventing SQL injection.

# Security and Adversarial Defense

A critical challenge in NLIDB systems is the risk of Prompt Injection attacks, where adversarial inputs manipulate the LLM into revealing sensitive data or executing unauthorized logic. HeyTrisha implements a multi-layered defense strategy:

**Schema Whitelisting**: The Adapter layer explicitly defines which entities and attributes are accessible. Sensitive columns—specifically authentication tokens, password hashes, and salt keys—are physically excluded from the context window.

**Pre-Execution Intent Validation**: The Core Engine includes a deterministic validation layer that operates after intent classification but before database execution. This "Guardrail Layer" evaluates the requested intent against a set of prohibited operations.

This hybrid approach—combining NLU-based Intent Filtering with Logic-Based Schema Constraints—ensures that the system remains resilient against social engineering attacks targeting the language model.

# Illustrative Example

To demonstrate the Intent-to-Execution pipeline, consider a typical analytical query:

**User Input**: "How many new customers joined last week?"

**Step 1: Core Processing (Intent Recognition)**

The Core engine extracts intent and temporal entities, producing a normalized JSON payload:

```json
{
  "intent": "customer_acquisition_count",
  "parameters": {
    "date_range": {
      "start": "2025-12-01 00:00:00",
      "end": "2025-12-07 23:59:59"
    },
    "filter": "new_users"
  }
}
```

**Step 2: Adapter Execution**

The WooCommerce Adapter maps the intent to a secure, parameterized query:

```php
public function get_customer_count($params) {
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(ID) FROM {$wpdb->users} 
         WHERE user_registered BETWEEN %s AND %s",
        $params['date_range']['start'],
        $params['date_range']['end']
    ));
}
```

**Step 3: Output**

The system returns: "42 new customers were acquired between Dec 1st and Dec 7th."

# Impact

HeyTrisha contributes to the scientific software ecosystem in three distinct ways:

1. **Reproducible Research in NLIDB**: It provides an open-source reference implementation for researchers studying the reliability of conversational interfaces in commercial settings. Researchers can modify the Core to test different NLP algorithms while relying on the stable Adapter for data retrieval.

2. **Legacy System Modernization**: The software demonstrates a non-intrusive method for adding AI capabilities to legacy CMS architectures without refactoring the underlying database schema.

3. **Educational Utility**: The clear separation of concerns (Core vs. Adapter) serves as an effective case study for software engineering curriculums focusing on modular design patterns and API integration.

# Acknowledgements

The author thanks early users and contributors who provided feedback during development.

# References
