@searching_products
Feature: Filtering list of products by taxon
    In order to see more specific list of the products
    As an Visitor
    I want to be able to filter the products

    Background:
        Given the store has 3 Mugs, 2 Stickers and 5 Books

    @domain
    Scenario: Filtering products by book
        When I filter them by "books" taxon
        Then I should see 5 products on the list

    @domain
    Scenario: Filtering product by stickers
        When I filter them by "stickers" taxon
        Then I should see 2 products on the list

    @domain
    Scenario: Filtering product by mugs
        When I filter them by "mugs" taxon
        Then I should see 3 products on the list

    @domain
    Scenario: List of all products without filtering
        When I view the list of the products without filtering
        Then I should see 10 products on the list
