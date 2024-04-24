# Product catalog API

This project implements a REST API endpoint that returns a list of products with discounts applied and optional filtering.
It's build using Laravel, MySQL database and includes Swagger documentation for easy API exploration.

## Features
- **Single endpoint:** provides a single endpoint for retrieving products
- **Filtering:** supports filtering by price and category
- **Discounts:** applies discounts based on predefined conditions
- **Documentation:** includes Swagger for easy API exploration
- **Tests:** Unit and feature tests are implemented to ensure code functionality

## Design decisions
- **Laravel:** chosen for its familiarity
- **Repository pattern with service layer:** chosen for its capacity to abstract data access, promote separation of concerns, and facilitate easier unit testing and maintainability within the application.

## Data

### Products table

| Column     | Type             | Null | Key | Default  |
|------------|------------------|------|-----|----------|
| id         | int(10) unsigned | NO   | PRI | NULL     |
| sku        | varchar(255)     | NO   | UNI | NULL     |
| name       | varchar(255)     | NO   |     | NULL     |
| category   | varchar(255)     | NO   |     | NULL     |
| created_at | timestamp        | YES  |     | NULL     |
| updated_at | timestamp        | YES  |     | NULL     |

### Prices table
| Column              | Type                | Null | Key | Default |
|---------------------|---------------------|------|-----|---------|
| id                  | bigint(20) unsigned | NO   | PRI | NULL    |
| original            | int(11)             | NO   |     | NULL    |
| final               | int(11)             | YES  |     | NULL    |
| discount_percentage | varchar(255)        | YES  |     | NULL    |
| currency            | char(3)             | NO   |     | USD     |
| product_id          | bigint(20) unsigned | NO   | MUL | NULL    |
| created_at          | timestamp           | YES  |     | NULL    |
| updated_at          | timestamp           | YES  |     | NULL    |

## Discounts
- Products with "insurance" category have a 30% discount
- The product with SKU `000003` has a 15% discount
- If the product with SKU `000003` belongs to the "insurance" category, it will have 30% discount

## Installation
1. Clone the repository:
```bash
git clone git@github.com:barbgluz/product-catalog.git
cd product-catalog
```

2. Install dependencies:

```bash
composer install
```
3. Set up your environment variables by renaming .env.example to .env and filling in the necessary configurations
4. Run migrations to set up the database schema:

```bash
php artisan migrate
```
5. Seed the database with the provided dataset:

```bash
php artisan db:seed
```

6. Start the Laravel development server:
```bash
php artisan serve
```
