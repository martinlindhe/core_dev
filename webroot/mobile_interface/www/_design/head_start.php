<?
	include('top.php');
?>
</head>
<body>
	<div id="headContent">
		<img alt="" src="/_objects/top_logo.jpg" style="position: absolute; top: 6px; left: 5px;" />
		<ul id="menu">
		<li><a href="<?=defined('ABORT_LNK')?ABORT_LNK:l('main', 'start')?>" onclick="return confirm('Säker ?');">avbryt</a></li>
		</ul>
	</div>
	<div id="contentContainer">
		<div id="wholeContent" style="text-align: center;">
