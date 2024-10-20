<?php

use CodeIgniter\Router\RouteCollection;
 use App\Controllers\Api\StudentController;
/**
 * @var RouteCollection $routes

 */
$routes->get('/', 'Home::index');


// API routes
$routes->group("api", ["namespace" => "App\Controllers\Api"], function($routes){
    $routes->post("create-student", [StudentController::class, "addStudent"]);

    //list student api
    $routes->get("students", [StudentController::class, "listStudents"]);

    //single student data
    $routes->get('student/(:num)', [StudentController::class, "getSingleStudentData"]);

    //update student api

    $routes->put("student/(:num)", [StudentController::class, "updateStudent"]);

    //delete student api

    $routes->delete("student/(:num)", [StudentController::class, "deleteStudent"]);

});




