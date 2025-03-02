<?= $renderer->render('header'); ?>

<h1>Bienvenue sur le blog</h1>
<ul>
    <li><a href="<?= $router->generateURL('blog.show', ['slug' => 'article-yolo-12']) ?>">Article 1</a></li>
    <li><a href="">Article 2</a></li>
    <li><a href="">Article 3</a></li>
    <li><a href="">Article 4</a></li>
</ul>

<?= $renderer->render('footer'); ?>
