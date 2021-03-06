sfPgLookPlugin
===============

This is symfony plugin that aims at providing a simple, fast & lightweight ORM based on PDO to take advantage of Postgresql features :

 *  Extra data types like key => value store, hierachical data and arrays, vectors, ISBN, IPV4 adresses etc...
 *  Regular expressions in SQL
 *  multiple tables inheritance
 *  schemas and views
 *  window functions
 *  stored procedures
 *  triggers
 *  million features I forget to mention here

Because the query language is SQL.

    PgLook::getMapFor('Book')->query(' SELECT * FROM book WHERE title ~ ? AND ? ~ ANY (tags)', array('postgresql', 'must read'));
    
    // same as 
    PgLook::getMapFor('Book')->findWhere('title ~ ? AND ? ~ ANY (tags)', array('postgresql', 'must read')); 
    
    // same as 
    $where = PgLookWhere::create('title ~ ?', array('postgresql')) 
      ->andWhere('? ANY (tags)', array('must read')); 
    
    PgLook::getMapFor('Book')->findPgLookWhere($where);

The abstraction layer turns data in your database into PHP objects in your code using a _translator_ :

 *  booleans in postgres will be boolean in PHP
 *  arrays in postgres will be arrays in PHP

For now, simple CRUD operations and basic forms are supported, no code generation, no admin gen, no schema, no fixtures, just a kernel to make easy, fast and efficient queries.

Queries return Collections, with handy iterator methods like in Propel 1.5 : isFirst, isLast, isOdd, getOddEven etc ...

I haven t tested it yet with PostGIS but it should work fine.

Last updated 18/05/2010
