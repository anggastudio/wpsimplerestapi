<?php
/**
 * Plugin Name: Simple WP Rest Api
 * Plugin URI: http://www.anggastudio.com/wp-plugins/simple-rest-api
 * Description: The Simplest WP REST API exist
 * Version: 1.0
 * Author: Angga
 * Author URI: https://www.anggastudio.com
 */
 
 function getPosts($param) {
     
    $numberposts = $param['numberposts'] ? $param['numberposts'] : 10;
    $page = $param['page'] ? ($param['page'] - 1) * $numberposts  : 0;
    $posts_type = $param['posts_type'] ? $param['posts_type'] : 'post';
    $categoryID = $param['category'] ? get_cat_ID($param['category']) : 0;
    
    $args = [
        'numberposts' => $numberposts,
        'posts_type' => $posts_type,
        'category' => $category,
        'offset' => $page
    ];
    
    $data = getNormalizePosts($args);
	return $data;
 }
 
 function getNormalizePosts($args){
    $posts = get_posts($args);
    $data = [];
    $i = 0;
    
    foreach($posts as $post) {
		$data[$i]['id'] = $post->ID;
		$data[$i]['title'] = $post->post_title;
		$data[$i]['slug'] = $post->post_name;
		$data[$i]['date'] = $post->post_date;
		$data[$i]['categories'] = getPostCategories($post->ID);
		$data[$i]['author'] = get_the_author_meta( $field = 'nicename', $user_id = $post->post_author );
		$data[$i]['comment_count'] = $post->comment_count;
		$data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
		$data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
		$data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
		$i++;
	}

	return $data;
 }
 
 function getPostCategories($postId) {
     $cats = get_the_category($postId);
     $categoryNames = [];
    
    foreach($cats as $cat) {
        array_push($categoryNames, $cat->name);
	}
     
     return $categoryNames;
 }
 
 function getSinglePost( $param ) {
	$args = [
		'name' => $param['slug'],
		'post_type' => 'post'
	];

	$post = get_posts($args);


	$data['content'] = $post[0]->post_content;
	$data[$i]['id'] = $post->ID;
	$data[$i]['title'] = $post->post_title;
	$data[$i]['slug'] = $post->post_name;
	$data[$i]['date'] = $post->post_date;
	$data[$i]['categories'] = getPostCategories($post->ID);
	$data[$i]['author'] = get_the_author_meta( $field = 'nicename', $user_id = $post->post_author );
	$data[$i]['comment_count'] = $post->comment_count;
	$data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
	$data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
	$data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'large');

	return $data;
 }

 function getCategories($param) {
    $categories = get_categories();
    
    $data = [];
    $i = 0;
    
    foreach($categories as $category) {
		$data[$i]['id'] = $category->cat_ID;
		$data[$i]['name'] = $category->name;
		$data[$i]['parent'] = $category->parent;
		$data[$i]['postCount'] = $category->count;
		$data[$i]['description'] = $category->description;
		$data[$i]['slug'] = $category->slug;
		$i++;
	}

	return $data;
 }
 
 add_action('rest_api_init', function() {
     
    register_rest_route('api/v1', 'posts', [
        'methods' => GET,
        'callback' => 'getPosts',
    ]);
    
    register_rest_route( 'api/v1', 'post/(?P<slug>[a-zA-Z0-9-]+)', array(
    	'methods' => 'GET',
    	'callback' => 'getSinglePost',
    ));
    
    register_rest_route( 'api/v1', 'categories', array(
    	'methods' => 'GET',
    	'callback' => 'getCategories',
    ));
    
 });
