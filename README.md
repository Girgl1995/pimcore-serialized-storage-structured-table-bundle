# Serialized Storage Structured Table for Pimcore

An alternative implementation of Pimcore's **Structured Table** data type that stores the table data in **serialized form** instead of creating a separate database column for every row/column combination.

## Why?

If you are familiar with Pimcore's Structured Table data type, you know that its database storage can become expensive in terms of the number of columns it creates.

Pimcore's documentation states:

> Each row and column combination creates a new column in the structured table's database table.

It also warns:

> Avoid this data type for tables with many rows or columns. The maximum number of database columns per table applies as a technical restriction.

This means that a Structured Table with many rows can quickly result in a large number of database columns.

For example, a table containing employee data might look like this in the Pimcore database:

| Table__emp_0__first_name | Table__emp_0__last_name | Table__emp_0__email | Table__emp_0__phone_number | Table__emp_1__first_name | Table__emp_1__last_name | ... |
|---|---|---|---|---|---|---|
| Robbert | Eggers | robberteggers@test.com | +493820583923 | Stanley | Kubrick | ... |

Every row/column combination results in another column.

With sufficiently large Structured Tables, the objects database table can therefore become very wide. This can eventually result in database errors such as:

```text
SQLSTATE[42000]: Syntax error or access violation: 1118
Row size too large. The maximum row size for the used table type,
not counting BLOBs, is 65535.
```

## The Alternative

This project provides an alternative Structured Table implementation that uses a different storage strategy.

Instead of storing every cell in its own database column, the complete Structured Table data is **serialized and stored in a single database column**.

The database representation therefore changes from many columns such as:

```text
Table__emp_0__first_name
Table__emp_0__last_name
Table__emp_0__email
Table__emp_0__phone_number
Table__emp_1__first_name
Table__emp_1__last_name
Table__emp_1__email
Table__emp_1__phone_number
...
```

to a single column:

```text
Table__data
```

The actual table data is stored inside this column in serialized json form, for example:

```text
{"emp_0":{"first_name":"Robert","last_name":"Eggers","phone".....
```

This means that **one Serialized Storage Structured Table requires only one column**, regardless of how many rows the table contains.

## Benefits

- **One database column per Serialized Storage Structured Table**
- No individual database column for every table cell
- Significantly reduces database schema growth
- Allows classes to contain substantially more table definitions
- Reduces the risk of reaching database column-count and row-size limitations
- Particularly useful for objects that require many structured tables

## What stays the same?

The goal is to keep the Structured Table experience in Pimcore while changing how the data is persisted.

The main difference is the database storage layer:

| | Pimcore Structured Table | Alternative Structured Table |
|---|---|--------------------|
| Pimcore editor | Structured Table | Structured Table   |
| Storage | Individual database columns | Serialized data    |
| Database columns | Grows with row/column combinations | **1 column per table** |
| Schema growth | High | Minimal            |


## When should you use it?

The Serialized Storage Structured Table can be useful when:

- a class contains many Structured Tables
- individual Structured Tables contain many rows and columns
- the Pimcore object_store_* table is approaching database column limits

## SQL Queries & JSON Support

Because the table data is stored in a dedicated JSON column, it can be queried using MySQL's native JSON functions and operators.

## Disclaimer

This project is an alternative implementation and is not intended to replace Pimcore's native Structured Table in every use case.

Please test the data type thoroughly with your Pimcore version and database configuration before using it in production.

## Installation

### 1. Add the repository

The bundle is installed through a VCS repository.

Add the following repository to the `composer.json` of your Pimcore project:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:Girgl1995/pimcore-serialized-storage-structured-table-bundle.git"
    }
]
```

### 2. Install the bundle

Install the bundle using Composer:

```bash
composer require factotum/serialized-storage-structured-table-bundle:dev-master
```

### 3. Register the bundle

After installing the bundle, register it in `config/bundles.php`:

```php
// ...
use Factotum\SerializedStorageStructuredTableBundle\PimcoreSerializedStorageStructuredTableBundle;

return [
    // ...
    PimcoreSerializedStorageStructuredTableBundle::class => ['all' => true]
];
```

### 4. Install the assets

After registering the bundle, install its assets:

```bash
bin/console assets:install
```

### 5. Clear the Cache

After completing the changes, clear the cache:

```bash
bin/console cache:clear
bin/console pimcore:cache:clear
```
