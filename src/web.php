<?php

use App\Router;

Router::get("/", "MainController@indexPage");
Router::get("/store", "MainController@storePage");

// 유저 정보
Router::post("/sign-in", "UserController@signIn");
Router::post("/sign-up", "UserController@signUp");
Router::get("/logout", "UserController@logout", "user");

// 온라인 집들이
Router::get("/online-party", "MainController@partyPage", "user");
Router::post("/knowhows", "MainController@writeKnowhow", "user");
Router::post("/knowhows/reviews", "MainController@reviewKnowhow", "user");

// 전문가 페이지
Router::get("/experts", "UserController@expertPage", "user");
Router::post("/experts/reviews", "UserController@reviewExpert", "user");

// 시공 견적
Router::get("/estimates", "MainController@estimatePage", "user");
Router::post("/requests", "MainController@writeRequest", "user");
Router::post("/responses", "MainController@writeResponse", "user");
Router::get("/responses", "MainController@getResponses", "user");
Router::post("/estimates/pick", "MainController@pickEstimate", "user");

Router::connect();