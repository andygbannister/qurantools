-- shows the size in rows and Mb of all the tables in quran schema
SELECT CONCAT(table_schema, '.', table_name) `Table Name`,
       CONCAT(ROUND(table_rows))                                                `Row Count`,
       CONCAT(ROUND(data_length / ( 1024 * 1024 ), 2), 'Mb')                    `Data Size`,
       CONCAT(ROUND(index_length / ( 1024 * 1024 ), 2), 'Mb')                   `Index Size`,
       CONCAT(ROUND(( data_length + index_length ) / ( 1024 * 1024 ), 2), 'Mb') `Total Size`,
       ROUND(index_length / data_length, 2)                                     `Index Fraction`
FROM   information_schema.TABLES
WHERE  TABLE_SCHEMA = 'quran'
ORDER  BY data_length + index_length DESC;