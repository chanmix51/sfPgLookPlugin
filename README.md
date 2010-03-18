sfPgLookPlugin
===============

This plugin is aimed at providing a fast & lightweight ORM based on PDO to take advantage of Postgresql features :
 * Extra data types like key => value store, hierachical data and arrays, ISBN, IPV4 adresses etc...
 * Regular expressions 
 * multiple tables inheritance
 * window functions
 * stored procedures
 * triggers
 * million features I forget to mention here

Because the query language is raw SQL.

The abstraction layer turns data in your database into PHP objects in your code using a _translator_ :
 * booleans in postgres will be boolean in PHP
 * arrays in postgres will be arrays in PHP

For now, simple CRUD operations are merely supported, no code generation, no admin gen, no schema, no fixtures, just a kernel to make fast and efficient queries.
