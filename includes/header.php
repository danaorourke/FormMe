<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Welcome to Cup of Tea Creations<?php echo (isset($title)) ? ' | ' . $title :''; ?></title>
	<meta name="description" content="We are a small creative agency with the goal of making your business look its best using forward-thinking design and clean, standards-compliant code.">
	<meta name="keywords" content="portland web dev, pdx web dev, portland web development">
	
	<link rel="shortcut icon" href="/favicon.ico" type="image/ico">
	<link href="/css/style.css" rel="stylesheet" type="text/css">
	<link href="/css/colorbox.css" rel="stylesheet" type="text/css">
	
	<!--[if lt IE 9]>
	<script src= "//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="/js/jquery.colorbox-min.js"></script>
	<script type="text/javascript">$(document).ready(function(){$(".portfolio").colorbox({rel:'portfolio'});});</script>
</head>
<body<?php if(isset($bodyid)){echo " id='$bodyid'";} ?>>
	<header id="header">
		<h1><a href="/index.php"><img src="img/logo.png" alt="Cup of Tea Creations" id="logo"></a></h1>
		<nav>
			<ul>
				<li><a href="/#about">about</a></li>
				<li><a href="/#services">services</a></li>
				<li><a href="/#portfolio">portfolio</a></li>
				<li><a href="/#contact">contact</a></li>
			</ul>
		</nav>
	</header>
	
	<div class="wrap">