<?php
    header("Content-type: text/css; charset: UTF-8");
?>
/* File: common.css.php */
/****************************************/

/* general tags */
html {
    font-size: 100%;
	color: #FFFFFF;
	font-family: Calibri;
}

a {
	color: #17731D;
}

body {
	background-color: #202020;
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
	color: #9AD19E;
	line-height: 90%;
	background-color: #35A33C;
	padding: 5px 70px;
	text-shadow: -1px -1px #888;
	-moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
}

div#navbar {
	margin: 0;
	border: 0;
	padding-left: 40px;
	background-color: #35A33C;
}

div#right_float_wrapper {
		float: right;
		display: inline-block;
}

div#admin_block, div#logout_block  {
	font-size: 120%;
	line-height: 120%;
	font-weight: bold;
	color: #FFFFFF;
	display: inline;
	margin-left: 1.5em;
}

div#logout_block a:visited, div#admin_block a:visited{
	color: #FFFFFF;
}

div#navbar p.navcurrent{
	display: inline;
	color: #FFFFFF;
	padding: 0 40px;
	background-color: #B8EC69;
	margin-right: 5px;
	font-size: 120%;
	font-weight: bold;
}

div#navbar p.navcurrent a:visited{
	color: #6F5C19;
}

div#navbar p.navlink{
	display: inline;
	color: #8CBE3E;
	padding: 0 40px;
	background-color: #2B4207;
	margin-right: 5px;
	font-size: 120%;
	font-weight: bold;
}

div#navbar p.navlink a{
	color: #8CBE3E;
}

div#banner h1{
	font-size: 300%;
	text-shadow: 2px 2px #000 inset;
	-moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
}

div#container{
	margin: 0 2em;
	line-height: 75%;
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
	color: #FFFFFF;
}

body#class_details div#scroll_form_edit_grade{
	top: -165px; /* start off the screen */
	width: 20%;
	position: fixed;
	z-index: 101;
	margin: 0 0 0 -10%;
	left: 50%;
	height: 160px;
	display: box;
	background-color: #465150;
	color: white;
	border: #aaa solid 2px;
	border-radius: 10px 10px 10px 10px;
    -moz-border-radius: 10px 10px 10px 10px;
    -webkit-border-radius: 10px 10px 10px 10px;
}

body#class_details div#scroll_form_add_assignment{
	top: -165px; /* start off the screen */
	width: 20%;
	position: fixed;
	z-index: 102;
	margin: 0 0 0 -10%;
	left: 50%;
	height: 160px;
	display: box;
	background-color: #465150;
	color: white;
	border: #aaa solid 2px;
	border-radius: 10px 10px 10px 10px;
    -moz-border-radius: 10px 10px 10px 10px;
    -webkit-border-radius: 10px 10px 10px 10px;
}

body#class_details div#scroll_form_edit_grade img#edit-hideBtn{
	cursor: pointer;
}

body#class_details div#scroll_form_add_assignment img#add-hideBtn{
	cursor: pointer;
}

body#class_details div#scroll_form_edit_grade form div.centered{
	width: 90%;
	margin: 30px auto;
}

body#class_details div#scroll_form_add_assignment form div.centered{
	width: 90%;
	margin: 30px auto;
}

a.edit-grade{
	cursor: pointer;
}

body#dashboard div#class_summary{
	border: #aaa solid 2px;
	line-height: 130%;
	border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
	padding: 0.8em 1.2em;
	background: #83DBD1;
	text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #000 inset;
    -webkit-box-shadow: 1px 1px 2px #000 inset;
    box-shadow: 1px 1px 6px #fff inset;
	width: 42%;
	display: inline-block;
	vertical-align: top;
	margin-left: 4%;
}

body#dashboard div#class_summary h2{
	color: #404040;
}

body#dashboard div#class_summary hr, body#dashboard div#point_summary hr{
	position: relative;
	top: -16px;
	padding: 0;
	margin: 0;
}

body#dashboard div#point_summary h2{
	color: #404040;
}

body#dashboard div#point_summary{
	border: #aaa solid 2px;
	line-height: 130%;
	border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
	padding: 0.8em 1.2em;
	background: #83DBD1;
	text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
    box-shadow: 1px 1px 6px #fff inset;
	width: 42%;
	display: inline-block;
	vertical-align: top;
	margin-right: 4%;
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
	color: #17731D;
}

body#dashboard div#point_summary div.class_rewards {
	display: inline-block;
	float: right;
	color: #17731D;
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

div.singleclass{
	font-size: 130%;
}

div.singleclass div.class_name {
	display: inline-block;
}

div.singleclass div.class_points {
	display: inline-block;
	color: #17731D;
}

div.singleclass div.class_letter_grade {
	display: inline-block;
	margin-left: 20px;
	width: 30px;
	color: #17731D;
}

div#class_summary div.singleclass div.wrapper {
	display: inline-block;
	float: right;
}

div.singleclass div.wrapper {
	display: inline-block;
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
	background: #83DBD1;
	text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #000 inset;
    -webkit-box-shadow: 1px 1px 2px #000 inset;
    box-shadow: 1px 1px 6px #fff inset;
	width: 45%;
	display: inline-block;
	vertical-align: top;
	margin-left: 2%;
}

body#class_details div#page_content div#container div#categories_section p a{
	color: #17731D;
}

body#class_details div#page_content div#container div#categories_section img.insert-grade{
	cursor: pointer;
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
    background: #83DBD1;
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

div#container fieldset a{
	color: #6F5C19;
}

div#page_content fieldset a{
	color: #6F5C19;
}