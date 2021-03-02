<?php require_once('../db.php'); ?>
<form action="search.php" method="get">
<!--    <input name="comment" type="text">-->
    <p><b>Сколько товаров вам нужно?</b></p>
    <?php for ($i = 1; $i <= DB::$maxAmount; $i++) :?>
        <input type="radio" name="product[amount]" value="<?php echo $i; ?>"><?php echo $i; ?><Br>
    <?php endfor; ?>
    <p><b>По какой цене?</b></p>
    <input type="text" name="product[price]">
    <?php foreach (DB::$productPropertiesTypeValues  as $key => $typeValues): ?>
        <p><b><?php echo $key; ?></b></p>
        <?php foreach ($typeValues as $value): ?>
            <input type="radio" name="product_properties[type][<?php echo $key; ?>]" value="<?php echo $value; ?>"><?php echo $value; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <br>
    <input type="submit" value="искать">
</form>
<ul>
<?php if ($results) {
  foreach ($results as $item):?>
        <li>
            <name></name>:
            <br>
            <?php echo $item["name"];?>
            <br>
        </li>
    <?php endforeach;
};?>
</ul>
