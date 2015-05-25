<?php
    header("Content-type: text/css; charset: UTF-8");
?>
/* File: common.css.php */
/****************************************/

/* general tags */
html {
    font-size: 100%;
	color: #033500;
	font-family: Calibri;
}

body {
	background-color: #7FAE7D;
	padding: 0;
	margin: 0;
}

body#indexlogin {
	background-color: #5E6C5D;
}

div#page_content{
	margin-top: 0;
	padding: 0;
}

body#indexlogin div#page_content{
	margin-top: 15%;
}

div#banner{
	margin: 0;
	border: 0;
	color: #465150;
	background-color: #5E6C5D;
	padding : 5px 30px;
	text-shadow: -1px -1px #888;
	-moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
}

div#navbar {
	margin: 0;
	border: 0;
	padding-left: 40px;
	background-color: #5E6C5D;
}

div#logout_block {
	float: right;
	font-size: 120%;
	font-weight: bold;
	color: #310708;
}

div#logout_block a:visited{
	color: #310708;
}

div#navbar p.navcurrent{
	display: inline;
	color: #FFFFFF;
	padding: 0 40px;
	background-color: #B95D14;
	margin-right: 5px;
	font-size: 120%;
	font-weight: bold;
}

div#navbar p.navlink{
	display: inline;
	color: #465150;
	padding: 0 40px;
	background-color: #FFF6EE;
	margin-right: 5px;
	font-size: 120%;
	font-weight: bold;
}

div#banner h1{
	font-size: 300%;
	text-shadow: 2px 2px #000 inset;
	-moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
}

div#container{
	margin: 0 2em;
}

body#indexlogin div#container{
	width: 30em;
	text-align: left;
	margin: 0 auto;
}



body#indexlogin div#container input[type=submit]{
	margin-left: auto;
	margin-right: auto;
	width: 5em;
	height: 2em;
	display: block;
}

body#create_account_form div#container{
	width: 50em;
	text-align: left;
	margin: 0 auto;
	line-height: 1.45;
}

body#create_account_form input[type=submit]{
	font-size: 110%;
}

body#create_account_form label{
	margin-left: 2em;
	font-size: 120%;
}

div#container div.wrapper h1{
	display: inline-block;
	color: #310708;
}

body#class_details div#scroll_form_edit_grade{
	top: -400px; /* start off the screen */
	width: 30%;
	position: fixed;
	margin: 0 0 0 -15%;
	left: 50%;
	height: 200px;
	display: box;
	background-color: #465150;
	color: white;
	border: #aaa solid 2px;
	border-radius: 10px 10px 10px 10px;
    -moz-border-radius: 10px 10px 10px 10px;
    -webkit-border-radius: 10px 10px 10px 10px;
}

body#class_details div#scroll_form_edit_grade form div.centered{
	width: 90%;
	margin: 30px auto;
}

span.edit-grade{
	cursor: pointer;
}

body#dashboard div#class_summary{
	border: #aaa solid 2px;
	border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
	padding: 0.4em 1.2em;
	background: #79A276;
	text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #000 inset;
    -webkit-box-shadow: 1px 1px 2px #000 inset;
    box-shadow: 1px 1px 6px #fff inset;
	width: 45%;
	display: inline-block;
	vertical-align: top;
	margin-left: 2%;
}

body#dashboard div#class_summary h2{
	color: #310708;
}

body#dashboard div#point_summary h2{
	color: #310708;
}

body#dashboard div#point_summary{
	border: #aaa solid 2px;
	border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
	padding: 0.4em 1.2em;
	background: #79A276;
	text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
    box-shadow: 1px 1px 6px #fff inset;
	width: 45%;
	display: inline-block;
	vertical-align: top;
	margin-right: 2%;
	float: right;
}

body#dashboard div#point_summary div.hide_cats{
	display: none;
}

body#dashboard div#point_summary div.pt_line_wrapper {
	font-size: 130%;
}

body#dashboard div#point_summary div.pt_class_line {
	display: inline-block;
}

body#dashboard div#point_summary div.class_rewards {
	display: inline-block;
	float: right;
}

body#dashboard div#point_summary div.pt_cat_line {
	margin-left: 0.75cm;
}

body#dashboard div#point_summary div.pt_cat_line div.pt_cat_name {
	display: inline-block;
}

body#dashboard div#point_summary div.pt_cat_line div.pt_cat {
	display: inline-block;
	float: right;
}

div#class_summary div.singleclass{
	font-size: 130%;
}

div#class_summary div.singleclass div.class_name {
	display: inline-block;
}

div#class_summary div.singleclass div.class_points {
	display: inline-block;
}

div#class_summary div.singleclass div.class_letter_grade {
	display: inline-block;
	margin-left: 20px;
	width: 30px;
}

div#class_summary div.singleclass div.wrapper {
	display: inline-block;
	float: right;
}

body#class_details div#container p.category {
	margin-left: 30px;
}

body#class_details div#container p.assignment_grade {
	margin-left: 60px;
}

p.field_error {
	display: inline;
	color: #B4161A;
	margin-left: 14px;
	font-size: 130%;
}

p.field_good {
	display: inline;
	color: green;
	margin-left: 14px;
	font-size: 130%;
}

body#class_details div#page_content div#container div#categories_section {
	border: #aaa solid 2px;
	border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
	padding: 0.4em 1.2em;
	background: #79A276;
	text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #000 inset;
    -webkit-box-shadow: 1px 1px 2px #000 inset;
    box-shadow: 1px 1px 6px #fff inset;
	width: 45%;
	display: inline-block;
	vertical-align: top;
	margin-left: 2%;
}


body#class_details div#page_content div#container iframe.category_details_window {
	padding: 0.4em 1.2em;
	width: 45%;
	height: 500px;
	display: inline-block;
	vertical-align: top;
	margin-right: 2%;
	float: right;
	scrolling: yes;
	border: 0px;
}

fieldset {
	
	/*font-size: 120%;
	font-weight: bolder;*/
    margin-top: 2em;
    border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
    border: #aaa solid 2px;
    padding: 1.2em;
    background: #79A276;
    text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
    box-shadow: 1px 1px 6px #fff inset;
}

legend {
	color: #033500;
	top: 12px;
	position: relative;
	font-size: 120%;
	font-weight: bolder;
}