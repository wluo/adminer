<?php
if ($_POST && !$error && !$_POST["add"] && !$_POST["change"] && !$_POST["change-js"]) {
	if ($_POST["drop"]) {
		query_redirect("ALTER TABLE " . idf_escape($_GET["foreign"]) . "\nDROP FOREIGN KEY " . idf_escape($_GET["name"]), $SELF . "table=" . urlencode($_GET["foreign"]), lang('Foreign key has been dropped.'));
	} else {
		$source = array_filter($_POST["source"], 'strlen');
		ksort($source);
		$target = array();
		foreach ($source as $key => $val) {
			$target[$key] = $_POST["target"][$key];
		}
		query_redirect("ALTER TABLE " . idf_escape($_GET["foreign"])
			. (strlen($_GET["name"]) ? "\nDROP FOREIGN KEY " . idf_escape($_GET["name"]) . "," : "")
			. "\nADD FOREIGN KEY (" . implode(", ", array_map('idf_escape', $source)) . ") REFERENCES " . idf_escape($_POST["table"]) . " (" . implode(", ", array_map('idf_escape', $target)) . ")"
			. (in_array($_POST["on_delete"], $on_actions) ? " ON DELETE $_POST[on_delete]" : "")
			. (in_array($_POST["on_update"], $on_actions) ? " ON UPDATE $_POST[on_update]" : "")
		, $SELF . "table=" . urlencode($_GET["foreign"]), (strlen($_GET["name"]) ? lang('Foreign key has been altered.') : lang('Foreign key has been created.')));
	}
}
page_header(lang('Foreign key'), $error, array("table" => $_GET["foreign"]), $_GET["foreign"]);

$tables = array();
$result = $dbh->query("SHOW TABLE STATUS");
while ($row = $result->fetch_assoc()) {
	if ($row["Engine"] == "InnoDB") {
		$tables[] = $row["Name"];
	}
}
$result->free();

if ($_POST) {
	$row = $_POST;
	ksort($row["source"]);
	if ($_POST["add"]) {
		$row["source"][] = "";
	} elseif ($_POST["change"] || $_POST["change-js"]) {
		$row["target"] = array();
	}
} elseif (strlen($_GET["name"])) {
	$foreign_keys = foreign_keys($_GET["foreign"]);
	$row = $foreign_keys[$_GET["name"]];
	$row["source"][] = "";
} else {
	$row = array("table" => $_GET["foreign"], "source" => array(""));
}

$source = get_vals("SHOW COLUMNS FROM " . idf_escape($_GET["foreign"])); //! no text and blob
$target = ($_GET["foreign"] === $row["table"] ? $source : get_vals("SHOW COLUMNS FROM " . idf_escape($row["table"])));
?>

<form action="" method="post">
<p>
<?php echo lang('Target table'); ?>:
<select name="table" onchange="this.form['change-js'].value = '1'; this.form.submit();"><?php echo optionlist($tables, $row["table"]); ?></select>
<input type="hidden" name="change-js" value="" />
</p>
<noscript><p><input type="submit" name="change" value="<?php echo lang('Change'); ?>" /></p></noscript>
<table cellspacing="0">
<thead><tr><th><?php echo lang('Source'); ?></th><th><?php echo lang('Target'); ?></th></tr></thead>
<?php
$j = 0;
foreach ($row["source"] as $key => $val) {
	echo "<tr>";
	echo "<td><select name='source[" . intval($key) . "]'" . ($j == count($row["source"]) - 1 ? " onchange='foreign_add_row(this);'" : "") . "><option></option>" . optionlist($source, $val) . "</select></td>";
	echo "<td><select name='target[" . intval($key) . "]'>" . optionlist($target, $row["target"][$key]) . "</select></td>";
	echo "</tr>\n";
	$j++;
}
?>
</table>
<p>
<?php echo lang('ON DELETE'); ?>: <select name="on_delete"><option></option><?php echo optionlist($on_actions, $row["on_delete"]); ?></select>
<?php echo lang('ON UPDATE'); ?>: <select name="on_update"><option></option><?php echo optionlist($on_actions, $row["on_update"]); ?></select>
</p>
<p>
<input type="hidden" name="token" value="<?php echo $token; ?>" />
<input type="submit" value="<?php echo lang('Save'); ?>" />
<?php if (strlen($_GET["name"])) { ?><input type="submit" name="drop" value="<?php echo lang('Drop'); ?>"<?php echo $confirm; ?> /><?php } ?>
</p>
<noscript><p><input type="submit" name="add" value="<?php echo lang('Add column'); ?>" /></p></noscript>
</form>