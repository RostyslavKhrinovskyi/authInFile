/* Для заданного списка товаров получить названия всех категорий, в которых представлены товары; */

 SELECT DISTINCT id, name FROM categories as `c`
 LEFT JOIN product_with_categories as `pc` ON pc.category_id = c.id
 WHERE pc.product_id IN('1','2', '3','5');


/* Для заданной категории получить список предложений всех товаров из этой категории и ее дочерних категорий;  */

 SELECT DISTINCT p.* FROM product_with_categories as ps
 LEFT JOIN products as p ON ps.product_id = p.id
 WHERE ps.category_id IN(
 SELECT c.id FROM categories AS c, categories AS parent
 WHERE c.left_key BETWEEN parent.left_key AND parent.right_key AND parent.name = 'phones'
 );

 /* Для заданного списка категорий получить количество предложений товаров в каждой категории; */

 SELECT category_id, COUNT(product_id) as `product_count` FROM product_with_categories
 WHERE category_id IN('4','5','8') GROUP BY category_id;


 /* Для заданного списка категорий получить общее количество уникальных предложений товара; */

 SELECT count(DISTINCT(p.id)) AS `count_products` FROM product_with_categories as ps
 LEFT JOIN products as p ON ps.product_id = p.id
 WHERE ps.category_id IN(
 SELECT c.id FROM categories AS c WHERE c.name IN('laptops', 'tablets', 'computers')
 );

 /* Для заданной категории получить ее полный путь в дереве (breadcrumb, «хлебные крошки»). */

