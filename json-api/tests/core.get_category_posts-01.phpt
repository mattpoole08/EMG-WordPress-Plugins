--TEST--
core.get_category_posts default
--FILE--
<?php

require_once 'HTTP/Client.php';
$http = new HTTP_Client();
$http->get('http://wordpress.test/?json=core.get_category_posts&slug=cat-a');
$response = $http->currentResponse();
echo $response['body'];

?>
--EXPECT--
{"status":"ok","count":2,"pages":1,"category":{"id":9,"slug":"cat-a","title":"Cat A","description":"","parent":0,"post_count":2},"posts":[{"id":358,"type":"post","slug":"readability-test","url":"http:\/\/wordpress.test\/2008\/09\/05\/readability-test\/","status":"publish","title":"Readability Test","title_plain":"Readability Test","content":"<p>All children, except one, grow up. They soon know that they will grow up, and the way Wendy knew was this. One day when she was two years old she was playing in a garden, and she plucked another flower and ran with it to her mother. I suppose she must have looked rather delightful, for Mrs. Darling put her hand to her heart and cried, &#8220;Oh, why can&#8217;t you remain like this for ever!&#8221; This was all that passed between them on the subject, but henceforth Wendy knew that she must grow up. You always know after you are two. Two is the beginning of the end.<\/p>\n<p> <a href=\"http:\/\/wordpress.test\/2008\/09\/05\/readability-test\/#more-358\" class=\"more-link\">Read more<\/a><\/p>\n","excerpt":"All children, except one, grow up. They soon know that they will grow up, and the way Wendy knew was this. One day when she was two years old she was playing in a garden, and she plucked another flower &hellip; <a href=\"http:\/\/wordpress.test\/2008\/09\/05\/readability-test\/\">Continue reading <span class=\"meta-nav\">&rarr;<\/span><\/a>","date":"2008-09-05 00:27:25","modified":"2008-09-05 00:27:25","categories":[{"id":9,"slug":"cat-a","title":"Cat A","description":"","parent":0,"post_count":2}],"tags":[{"id":53,"slug":"chattels","title":"chattels","description":"","post_count":2},{"id":82,"slug":"privation","title":"privation","description":"","post_count":2}],"author":{"id":3,"slug":"chip-bennett","name":"Chip Bennett","first_name":"","last_name":"","nickname":"Chip Bennett","url":"","description":""},"comments":[],"attachments":[],"comment_count":0,"comment_status":"closed"},{"id":188,"type":"post","slug":"layout-test","url":"http:\/\/wordpress.test\/2008\/09\/04\/layout-test\/","status":"publish","title":"Layout Test","title_plain":"Layout Test","content":"<p>This is a sticky post!!! Make sure it sticks!<\/p>\n<p>This should then split into other pages with layout, images, HTML tags, and other things.<\/p>\n","excerpt":"This is a sticky post!!! Make sure it sticks! This should then split into other pages with layout, images, HTML tags, and other things.","date":"2008-09-04 23:02:20","modified":"2008-09-04 23:02:20","categories":[{"id":3,"slug":"aciform","title":"aciform","description":"","parent":0,"post_count":2},{"id":9,"slug":"cat-a","title":"Cat A","description":"","parent":0,"post_count":2},{"id":10,"slug":"cat-b","title":"Cat B","description":"","parent":0,"post_count":1},{"id":11,"slug":"cat-c","title":"Cat C","description":"","parent":0,"post_count":1},{"id":41,"slug":"sub","title":"sub","description":"","parent":3,"post_count":1}],"tags":[{"id":93,"slug":"tag1","title":"tag1","description":"","post_count":1},{"id":94,"slug":"tag2","title":"tag2","description":"","post_count":1},{"id":95,"slug":"tag3","title":"tag3","description":"","post_count":1}],"author":{"id":3,"slug":"chip-bennett","name":"Chip Bennett","first_name":"","last_name":"","nickname":"Chip Bennett","url":"","description":""},"comments":[],"attachments":[],"comment_count":0,"comment_status":"closed"}]}
