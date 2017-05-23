@searching_products
Feature: Sorting list of products by name
    In order to see more sorted list of the products
    As an Visitor
    I want to be able to sort the products

    Background:
        Given the store has a product "Banana T-Shirt" priced at "$100" in "tablets" channel
        And the store also has a product "Star Wars T-Shirt" priced at "$150" in "mobile" channel
        And the store also has a product "LOTR T-Shirt" priced at "$300" in "mobile" channel
        And the store also has a product "Breaking Bad T-Shirt" priced at "$50" in "mobile" channel
        And the store also has a product "Westworld T-Shirt" priced at "$1000" in "europe" channel
        And the store also has a product "Orange T-Shirt" priced at "$1000" in "europe" channel

    @domain
    Scenario: Sorting products by name in ascending order
        When I sort them by name in ascending order
        Then I should see products in order like "Banana T-Shirt", "Breaking Bad T-Shirt", "LOTR T-Shirt", "Orange T-Shirt", "Star Wars T-Shirt", "Westworld T-Shirt"

    @domain
    Scenario: Sorting products by name in descending order
        When I sort them by name in descending order
        Then I should see products in order like "Westworld T-Shirt", "Star Wars T-Shirt", "Orange T-Shirt", "LOTR T-Shirt", "Breaking Bad T-Shirt", "Banana T-Shirt"
