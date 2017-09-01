TRUNCATE TABLE `oc_simple_blog_article`;

INSERT INTO `oc_simple_blog_article` (`simple_blog_article_id`, `simple_blog_author_id`, `allow_comment`, `image`, `featured_image`, `article_related_method`, `article_related_option`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES
(1, 1, 1, 'catalog/blog/block-image-02.jpg', 'catalog/blog/post-img.jpg', 'product_wise', '', 1, 1, '2015-03-19 15:27:29', '2015-04-02 14:01:55'),
(2, 1, 1, 'catalog/blog/block-image-03.jpg', 'catalog/blog/post-img1.jpg', 'product_wise', '', 2, 1, '2015-03-19 15:29:15', '2015-04-02 14:02:05'),
(3, 1, 1, 'catalog/blog/block-image-02.jpg', 'catalog/blog/post-img2.jpg', 'product_wise', '', 3, 1, '2015-03-19 15:48:02', '2015-04-02 14:02:14');


TRUNCATE TABLE `oc_simple_blog_article_description`;

INSERT INTO `oc_simple_blog_article_description` (`simple_blog_article_description_id`, `simple_blog_article_id`, `language_id`, `article_title`, `description`, `meta_description`, `meta_keyword`) VALUES ('5', '3', '1', 'Ut euismod, turpis sollicitudin lobortis pellente sque', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. Integer dictum est vitae sem.', '', '');
INSERT INTO `oc_simple_blog_article_description` (`simple_blog_article_description_id`, `simple_blog_article_id`, `language_id`, `article_title`, `description`, `meta_description`, `meta_keyword`) VALUES ('6', '3', '2', 'Ut euismod, turpis sollicitudin lobortis pellente sque', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. Integer dictum est vitae sem.', '', '');
INSERT INTO `oc_simple_blog_article_description` (`simple_blog_article_description_id`, `simple_blog_article_id`, `language_id`, `article_title`, `description`, `meta_description`, `meta_keyword`) VALUES ('7', '1', '1', 'Mauris lacinia lectus sit amet felis erat volutpat', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. Integer dictum est vitae sem.', '', '');
INSERT INTO `oc_simple_blog_article_description` (`simple_blog_article_description_id`, `simple_blog_article_id`, `language_id`, `article_title`, `description`, `meta_description`, `meta_keyword`) VALUES ('8', '1', '2', 'Mauris lacinia lectus sit amet felis erat volutpat', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. Integer dictum est vitae sem.', '', '');
INSERT INTO `oc_simple_blog_article_description` (`simple_blog_article_description_id`, `simple_blog_article_id`, `language_id`, `article_title`, `description`, `meta_description`, `meta_keyword`) VALUES ('9', '2', '1', 'Vivamus euismod nulla vel nunc.  Fusce tincidunt, justo congue egestas molestie', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. Integer dictum est vitae sem.', '', '');
INSERT INTO `oc_simple_blog_article_description` (`simple_blog_article_description_id`, `simple_blog_article_id`, `language_id`, `article_title`, `description`, `meta_description`, `meta_keyword`) VALUES ('10', '2', '2', 'Vivamus euismod nulla vel nunc.  Fusce tincidunt, justo congue egestas molestie', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. Integer dictum est vitae sem.', '', '');


TRUNCATE TABLE `oc_simple_blog_article_description_additional`;

INSERT INTO `oc_simple_blog_article_description_additional` (`simple_blog_article_id`, `language_id`, `additional_description`) VALUES ('3', '1', '&lt;div class=&quot;blockquote&quot;&gt;\r\n    &lt;div class=&quot;inside&quot;&gt;\r\n        &lt;p&gt;Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. &lt;/p&gt;\r\n\r\n    &lt;/div&gt;\r\n\r\n&lt;/div&gt;\r\n\r\n&lt;div class=&quot;text&quot;&gt;\r\n    &lt;p&gt;Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. &lt;/p&gt;\r\n\r\n    &lt;p&gt;Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. &lt;/p&gt;\r\n\r\n    &lt;div class=&quot;line-divider&quot;&gt;&lt;/div&gt;\r\n\r\n    &lt;h3&gt;TAGS&lt;/h3&gt;\r\n\r\n    &lt;ul class=&quot;tags-list&quot;&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/article/view&amp;amp;simple_blog_article_id=1&quot;&gt;Web Design&lt;/a&gt;&lt;/li&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/category&amp;amp;simple_blog_category_id=1&quot;&gt;Wordpress&lt;/a&gt;&lt;/li&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/article/view&amp;amp;simple_blog_article_id=2&quot;&gt;Animation&lt;/a&gt;&lt;/li&gt;\r\n\r\n    &lt;/ul&gt;\r\n\r\n&lt;/div&gt;');
INSERT INTO `oc_simple_blog_article_description_additional` (`simple_blog_article_id`, `language_id`, `additional_description`) VALUES ('3', '2', '&lt;div class=&quot;blockquote&quot;&gt;\r\n    &lt;div class=&quot;inside&quot;&gt;\r\n        &lt;p&gt;Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. &lt;/p&gt;\r\n\r\n    &lt;/div&gt;\r\n\r\n&lt;/div&gt;\r\n\r\n&lt;div class=&quot;text&quot;&gt;\r\n    &lt;p&gt;Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. &lt;/p&gt;\r\n\r\n    &lt;p&gt;Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. &lt;/p&gt;\r\n\r\n    &lt;div class=&quot;line-divider&quot;&gt;&lt;/div&gt;\r\n\r\n    &lt;h3&gt;TAGS&lt;/h3&gt;\r\n\r\n    &lt;ul class=&quot;tags-list&quot;&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/article/view&amp;amp;simple_blog_article_id=1&quot;&gt;Web Design&lt;/a&gt;&lt;/li&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/category&amp;amp;simple_blog_category_id=1&quot;&gt;Wordpress&lt;/a&gt;&lt;/li&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/article/view&amp;amp;simple_blog_article_id=2&quot;&gt;Animation&lt;/a&gt;&lt;/li&gt;\r\n\r\n    &lt;/ul&gt;\r\n\r\n&lt;/div&gt;');
INSERT INTO `oc_simple_blog_article_description_additional` (`simple_blog_article_id`, `language_id`, `additional_description`) VALUES ('2', '1', '&lt;div class=&quot;blockquote&quot;&gt;\r\n    &lt;div class=&quot;inside&quot;&gt;\r\n        &lt;p&gt;Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. &lt;/p&gt;\r\n\r\n    &lt;/div&gt;\r\n\r\n&lt;/div&gt;\r\n\r\n&lt;div class=&quot;text&quot;&gt;\r\n    &lt;p&gt;Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. &lt;/p&gt;\r\n\r\n    &lt;p&gt;Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. &lt;/p&gt;\r\n\r\n    &lt;div class=&quot;line-divider&quot;&gt;&lt;/div&gt;\r\n\r\n    &lt;h3&gt;TAGS&lt;/h3&gt;\r\n\r\n    &lt;ul class=&quot;tags-list&quot;&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/article/view&amp;amp;simple_blog_article_id=1&quot;&gt;Web Design&lt;/a&gt;&lt;/li&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/category&amp;amp;simple_blog_category_id=1&quot;&gt;Wordpress&lt;/a&gt;&lt;/li&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/article/view&amp;amp;simple_blog_article_id=2&quot;&gt;Animation&lt;/a&gt;&lt;/li&gt;\r\n\r\n    &lt;/ul&gt;\r\n\r\n&lt;/div&gt;');
INSERT INTO `oc_simple_blog_article_description_additional` (`simple_blog_article_id`, `language_id`, `additional_description`) VALUES ('2', '2', '&lt;div class=&quot;blockquote&quot;&gt;\r\n    &lt;div class=&quot;inside&quot;&gt;\r\n        &lt;p&gt;Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. &lt;/p&gt;\r\n\r\n    &lt;/div&gt;\r\n\r\n&lt;/div&gt;\r\n\r\n&lt;div class=&quot;text&quot;&gt;\r\n    &lt;p&gt;Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas eu enim in lorem scelerisque auctor. Ut non erat. Suspendisse fermentum posuere lectus. Fusce vulputate nibh egestas orci. Aliquam lectus. Morbi eget dolor sagittis. Morbi sit amet quam. Nulla libero. &lt;/p&gt;\r\n\r\n    &lt;p&gt;Donec et mi eu massa ultrices scelerisque. Nullam ac nisi non eros gravida venenatis. Ut euismod, turpis sollicitudin lobortis pellentesque, libero massa dapibus dui, eu dictum justo urna et mi. &lt;/p&gt;\r\n\r\n    &lt;div class=&quot;line-divider&quot;&gt;&lt;/div&gt;\r\n\r\n    &lt;h3&gt;TAGS&lt;/h3&gt;\r\n\r\n    &lt;ul class=&quot;tags-list&quot;&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/article/view&amp;amp;simple_blog_article_id=1&quot;&gt;Web Design&lt;/a&gt;&lt;/li&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/category&amp;amp;simple_blog_category_id=1&quot;&gt;Wordpress&lt;/a&gt;&lt;/li&gt;\r\n\r\n        &lt;li&gt;&lt;a href=&quot;index.php?route=simple_blog/article/view&amp;amp;simple_blog_article_id=2&quot;&gt;Animation&lt;/a&gt;&lt;/li&gt;\r\n\r\n    &lt;/ul&gt;\r\n\r\n&lt;/div&gt;');


TRUNCATE TABLE `oc_simple_blog_article_product_related`;



TRUNCATE TABLE `oc_simple_blog_article_to_category`;

INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('3', '1');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('3', '2');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('3', '3');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('3', '4');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('3', '5');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('3', '6');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('3', '7');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('3', '8');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('3', '9');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('1', '1');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('1', '2');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('1', '6');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('1', '9');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('2', '2');
INSERT INTO `oc_simple_blog_article_to_category` (`simple_blog_article_id`, `simple_blog_category_id`) VALUES ('2', '4');


TRUNCATE TABLE `oc_simple_blog_article_to_layout`;



TRUNCATE TABLE `oc_simple_blog_article_to_store`;

INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('3', '0');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('3', '5');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('3', '1');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('3', '6');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('3', '3');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('3', '4');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('3', '2');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('3', '7');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('1', '0');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('1', '5');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('1', '1');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('1', '6');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('1', '3');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('1', '4');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('1', '2');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('1', '7');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('2', '0');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('2', '5');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('2', '1');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('2', '6');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('2', '3');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('2', '4');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('2', '2');
INSERT INTO `oc_simple_blog_article_to_store` (`simple_blog_article_id`, `store_id`) VALUES ('2', '7');


TRUNCATE TABLE `oc_simple_blog_author`;

INSERT INTO `oc_simple_blog_author` (`simple_blog_author_id`, `name`, `image`, `status`, `date_added`, `date_modified`) VALUES ('1', 'Admin', '', '1', '2015-03-19 15:26:09', '2015-03-19 15:26:09');


TRUNCATE TABLE `oc_simple_blog_author_description`;

INSERT INTO `oc_simple_blog_author_description` (`simple_blog_author_description_id`, `simple_blog_author_id`, `language_id`, `description`, `meta_description`, `meta_keyword`, `date_added`) VALUES ('1', '1', '1', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '', '0000-00-00 00:00:00');
INSERT INTO `oc_simple_blog_author_description` (`simple_blog_author_description_id`, `simple_blog_author_id`, `language_id`, `description`, `meta_description`, `meta_keyword`, `date_added`) VALUES ('2', '1', '2', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '', '0000-00-00 00:00:00');


TRUNCATE TABLE `oc_simple_blog_category`;

INSERT INTO `oc_simple_blog_category` (`simple_blog_category_id`, `image`, `parent_id`, `top`, `blog_category_column`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('1', '', '0', '0', '0', '10', '1', '1', '2015-03-19 15:30:18', '2015-03-19 15:31:29');
INSERT INTO `oc_simple_blog_category` (`simple_blog_category_id`, `image`, `parent_id`, `top`, `blog_category_column`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('2', '', '0', '0', '0', '10', '2', '1', '2015-03-19 15:31:18', '2015-03-19 15:31:18');
INSERT INTO `oc_simple_blog_category` (`simple_blog_category_id`, `image`, `parent_id`, `top`, `blog_category_column`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('3', '', '0', '0', '0', '10', '3', '1', '2015-03-19 15:32:28', '2015-03-19 15:32:28');
INSERT INTO `oc_simple_blog_category` (`simple_blog_category_id`, `image`, `parent_id`, `top`, `blog_category_column`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('4', '', '0', '0', '0', '10', '4', '1', '2015-03-19 15:33:16', '2015-03-19 15:33:16');
INSERT INTO `oc_simple_blog_category` (`simple_blog_category_id`, `image`, `parent_id`, `top`, `blog_category_column`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('5', '', '0', '0', '0', '10', '5', '1', '2015-03-19 15:34:14', '2015-03-19 15:34:14');
INSERT INTO `oc_simple_blog_category` (`simple_blog_category_id`, `image`, `parent_id`, `top`, `blog_category_column`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('6', '', '0', '0', '0', '10', '6', '1', '2015-03-19 15:34:41', '2015-03-19 15:34:41');
INSERT INTO `oc_simple_blog_category` (`simple_blog_category_id`, `image`, `parent_id`, `top`, `blog_category_column`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('7', '', '0', '0', '0', '10', '7', '1', '2015-03-19 15:35:04', '2015-03-19 15:35:04');
INSERT INTO `oc_simple_blog_category` (`simple_blog_category_id`, `image`, `parent_id`, `top`, `blog_category_column`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('8', '', '0', '0', '0', '10', '8', '1', '2015-03-19 15:45:19', '2015-03-19 15:45:19');
INSERT INTO `oc_simple_blog_category` (`simple_blog_category_id`, `image`, `parent_id`, `top`, `blog_category_column`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('9', '', '0', '0', '0', '10', '9', '1', '2015-03-19 15:45:43', '2015-03-19 15:45:43');


TRUNCATE TABLE `oc_simple_blog_category_description`;

INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('3', '2', '1', 'Daily Inspiration', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('4', '2', '2', '??????? ??????', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('5', '1', '1', 'Audios', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('6', '1', '2', '??????', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('7', '3', '1', 'Freelance', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('8', '3', '2', '?????', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('9', '4', '1', 'Links', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('10', '4', '2', '?????', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('11', '5', '1', 'Mobile', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('12', '5', '2', '?????', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('13', '6', '1', 'Photography', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('14', '6', '2', '?????', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('15', '7', '1', 'Quotes', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('16', '7', '2', '?????', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('17', '8', '1', 'Resources', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('18', '8', '2', '?????', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('19', '9', '1', 'Status', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');
INSERT INTO `oc_simple_blog_category_description` (`simple_blog_category_description_id`, `simple_blog_category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('20', '9', '2', '???', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', '');


TRUNCATE TABLE `oc_simple_blog_category_to_layout`;



TRUNCATE TABLE `oc_simple_blog_category_to_store`;

INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('2', '0');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('2', '5');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('2', '1');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('2', '6');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('2', '3');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('2', '4');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('2', '2');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('2', '7');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('1', '0');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('1', '5');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('1', '1');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('1', '6');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('1', '3');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('1', '4');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('1', '2');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('1', '7');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('3', '0');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('3', '5');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('3', '1');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('3', '6');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('3', '3');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('3', '4');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('3', '2');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('3', '7');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('4', '0');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('4', '5');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('4', '1');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('4', '6');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('4', '3');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('4', '4');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('4', '2');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('4', '7');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('5', '0');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('5', '5');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('5', '1');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('5', '6');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('5', '3');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('5', '4');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('5', '2');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('5', '7');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('6', '0');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('6', '5');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('6', '1');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('6', '6');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('6', '3');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('6', '4');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('6', '2');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('6', '7');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('7', '0');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('7', '5');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('7', '1');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('7', '6');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('7', '3');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('7', '4');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('7', '2');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('7', '7');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('8', '0');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('8', '5');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('8', '1');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('8', '6');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('8', '3');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('8', '4');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('8', '2');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('8', '7');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('9', '0');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('9', '5');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('9', '1');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('9', '6');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('9', '3');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('9', '4');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('9', '2');
INSERT INTO `oc_simple_blog_category_to_store` (`simple_blog_category_id`, `store_id`) VALUES ('9', '7');


TRUNCATE TABLE `oc_simple_blog_comment`;

INSERT INTO `oc_simple_blog_comment` (`simple_blog_comment_id`, `simple_blog_article_id`, `simple_blog_article_reply_id`, `author`, `comment`, `status`, `date_added`, `date_modified`) VALUES ('1', '2', '0', 'John Doe', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus ', '1', '2015-03-19 15:59:45', '2015-03-19 15:59:55');
INSERT INTO `oc_simple_blog_comment` (`simple_blog_comment_id`, `simple_blog_article_id`, `simple_blog_article_reply_id`, `author`, `comment`, `status`, `date_added`, `date_modified`) VALUES ('2', '2', '1', 'Susan', 'Ut sit amet turpis. In est a', '0', '2015-03-19 16:00:20', '2015-03-19 16:00:20');
INSERT INTO `oc_simple_blog_comment` (`simple_blog_comment_id`, `simple_blog_article_id`, `simple_blog_article_reply_id`, `author`, `comment`, `status`, `date_added`, `date_modified`) VALUES ('3', '2', '1', 'Susan', 'Ut sit amet turpis. In est a', '1', '2015-03-19 16:01:25', '2015-03-19 16:01:25');
INSERT INTO `oc_simple_blog_comment` (`simple_blog_comment_id`, `simple_blog_article_id`, `simple_blog_article_reply_id`, `author`, `comment`, `status`, `date_added`, `date_modified`) VALUES ('4', '2', '1', 'Morgan Freeman', 'In est arcu, sollicitudin eu, vehicula venenatis, tempor vitae, est. Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatib', '1', '2015-03-19 16:01:49', '2015-03-19 16:01:49');
INSERT INTO `oc_simple_blog_comment` (`simple_blog_comment_id`, `simple_blog_article_id`, `simple_blog_article_reply_id`, `author`, `comment`, `status`, `date_added`, `date_modified`) VALUES ('5', '2', '0', 'Kenneth Longery', 'Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula ve', '1', '2015-03-19 16:02:15', '2015-03-19 16:02:15');
INSERT INTO `oc_simple_blog_comment` (`simple_blog_comment_id`, `simple_blog_article_id`, `simple_blog_article_reply_id`, `author`, `comment`, `status`, `date_added`, `date_modified`) VALUES ('6', '1', '0', 'Heather', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec eros tellus, scelerisque nec, rhoncus eget, laoreet sit amet, nunc. Ut sit amet turpis. In est arcu, sollicitudin eu, vehicula ve', '1', '2015-03-20 13:08:58', '2015-03-20 13:08:58');
INSERT INTO `oc_simple_blog_comment` (`simple_blog_comment_id`, `simple_blog_article_id`, `simple_blog_article_reply_id`, `author`, `comment`, `status`, `date_added`, `date_modified`) VALUES ('7', '1', '6', 'Noris', 'Praesent vitae dui. Morbi id tellus. Cum sociis natoque penatibus et magnis dis parturien', '1', '2015-03-20 13:09:19', '2015-03-20 13:09:19');


TRUNCATE TABLE `oc_simple_blog_related_article`;



TRUNCATE TABLE `oc_simple_blog_view`;

INSERT INTO `oc_simple_blog_view` (`simple_blog_view_id`, `simple_blog_article_id`, `view`, `date_added`, `date_modified`) VALUES ('1', '2', '48', '2015-03-19 15:59:09', '2015-04-02 12:11:41');
INSERT INTO `oc_simple_blog_view` (`simple_blog_view_id`, `simple_blog_article_id`, `view`, `date_added`, `date_modified`) VALUES ('2', '1', '46', '2015-03-19 16:06:08', '2015-04-02 12:11:42');
INSERT INTO `oc_simple_blog_view` (`simple_blog_view_id`, `simple_blog_article_id`, `view`, `date_added`, `date_modified`) VALUES ('3', '3', '54', '2015-03-19 17:58:16', '2015-04-02 12:11:39');

INSERT INTO `oc_layout` (`layout_id`, `name`) VALUES
(25, 'Simple Blog Posts'),
(26, 'Simple Blog Single Post'),
(27, 'Simple Blog Category'),
(28, 'Simple Blog Author');

INSERT INTO `oc_layout_module` (`layout_module_id`, `layout_id`, `code`, `position`, `sort_order`) VALUES
(null, 1, 'simple_blog', 'content_bottom', 2),
(null, 25, 'simple_blog_category', 'column_right', 2),
(null, 26, 'simple_blog_category', 'column_right', 2),
(null, 27, 'simple_blog_category', 'column_right', 2),
(null, 28, 'simple_blog_category', 'column_right', 2);

INSERT INTO `oc_layout_route` (`layout_route_id`, `layout_id`, `store_id`, `route`) VALUES
(null, 25, 0, 'simple_blog/article'),
(null, 26, 0, 'simple_blog/article/view'),
(null, 27, 0, 'simple_blog/category'),
(null, 28, 0, 'simple_blog/author');


INSERT INTO `oc_layout_module` (`layout_module_id`, `layout_id`, `code`, `position`, `sort_order`) VALUES
(null, 1, 'simple_blog', 'content_bottom', 2),
(null, 25, 'simple_blog_category', 'column_right', 2),
(null, 26, 'simple_blog_category', 'column_right', 2),
(null, 27, 'simple_blog_category', 'column_right', 2),
(null, 28, 'simple_blog_category', 'column_right', 2);

INSERT INTO `oc_setting` (`setting_id`, `store_id`, `code`, `key`, `value`, `serialized`) VALUES
(null, 0, 'simple_blog', 'simple_blog_status', '1', 0),
(null, 0, 'simple_blog', 'simple_blog_seo_keyword', 'blog', 0),
(null, 0, 'simple_blog', 'simple_blog_footer_heading', 'Blog', 0),
(null, 0, 'simple_blog', 'simple_blog_heading', 'Blog posts', 0),
(null, 0, 'simple_blog', 'simple_blog_product_related_heading', 'Product related', 0),
(null, 0, 'simple_blog', 'simple_blog_comment_related_heading', 'Comments', 0),
(null, 0, 'simple_blog', 'simple_blog_image_width', '176', 0),
(null, 0, 'simple_blog', 'simple_blog_image_height', '119', 0),
(null, 0, 'simple_blog', 'simple_blog_short_description_length', '200', 0),
(null, 0, 'simple_blog', 'simple_blog_display_category', '0', 0),
(null, 0, 'simple_blog', 'simple_blog_comment_auto_approval', '1', 0),
(null, 0, 'simple_blog', 'simple_blog_author_information', '0', 0),
(null, 0, 'simple_blog', 'simple_blog_related_articles', '0', 0),
(null, 0, 'simple_blog', 'simple_blog_share_social_site', '1', 0),
(null, 0, 'simple_blog', 'simple_blog_module', 'a:1:{s:16:"tk1glk5jmjss8aor";a:4:{s:13:"article_limit";s:1:"4";s:11:"category_id";s:3:"all";s:6:"status";s:1:"1";s:10:"sort_order";s:0:"";}}', 1),
(null, 0, 'simple_blog_category', 'simple_blog_category_status', '1', 0),
(null, 0, 'simple_blog_category', 'simple_blog_category_search_article', '0', 0),
(null, 0, 'simple_blog_category', 'simple_blog_category_module', 'a:1:{s:16:"6k2mdk6v02ne9udi";a:2:{s:6:"status";s:1:"1";s:10:"sort_order";s:1:"1";}}', 1);



