<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class StudentController extends ResourceController
{
    protected $modelName = "App\Models\StudentModel";
    protected $format = "json";

    // Add student POST
    public function addStudent()
    {
        $data = $this->request->getPost();
        $name = $data['name'] ?? "";
        $email = $data['email'] ?? "";
        $phone_no = $data['phone_no'] ?? "";

        if (empty($name) || empty($email)) {
            return $this->respond([
                "status" => false,
                "message" => "Please provide the required fields (name, email)"
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Check if student already exists
        $student_data = $this->checkStudentByEmail($email);

        if (!empty($student_data)) {
            return $this->respond([
                "status" => false,
                "message" => "Student already exists"
            ], ResponseInterface::HTTP_CONFLICT);
        } else {
            // Add student to table
            $inserted = $this->model->insert([
                "name" => $name,
                "email" => $email,
                "phone_no" => $phone_no
            ]);

            if ($inserted) {
                return $this->respond([
                    "status" => true,
                    "message" => "Student successfully added"
                ], ResponseInterface::HTTP_CREATED);
            } else {
                return $this->respond([
                    "status" => false,
                    "message" => "Failed to add student"
                ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    // List all students GET
    public function listStudents()
    {
        $students = $this->model->findAll();
        return $this->respond([
            "status" => true,
            "message" =>"Students data",
            "data" => $students
        ], ResponseInterface::HTTP_OK);
    }

    // Show single student data GET
    public function getSingleStudentData($student_id)
    {
        $student_data = $this->model->find($student_id);

        if (!empty($student_data)) {
            return $this->respond([
                "status" => true,
                "message" => "Student data",
                "data" => $student_data
            ]);
        }else{

        return $this->respond([
            "status" => false,
            "message" => "Student not found"
        ]);
    }
    }
    // Update student data PUT
    public function updateStudent($student_id)
    {
        $student = $this->model->find($student_id);
        if (!empty($student)) {

            $data = json_decode(file_get_contents("php://input"), true);

            $updated_data = [
                "name" => isset($data['name']) && !empty($data['name']) ? $data['name'] : $student['name'],
                "email" => isset($data['email']) && !empty($data['email']) ? $data['email'] : $student['email'],
                "phone_no" => isset($data['phone_no']) && !empty($data['phone_no']) ? $data['phone_no'] : $student['phone_no']
            ];
            if($this->model->update($student_id, $updated_data)){
                return $this->respond([
                    "status" => true,
                    "message" => "Student data updated succesfully"
                ]);
            }else{
                return $this->respond([
                    "status" => false,
                    "message" => "Failed to update data"
                ]);
            }
            
        }else{

        return $this->respond([
            "status" => false,
            "message" => "Student not found"
        ]);
    }
}
    // Delete student data DELETE
    public function deleteStudent($student_id)
    {
        $student = $this->model->find($student_id);
        if (!empty($student)) {
            if($this->model->delete($student_id)){
            return $this->respond([
            "status" => true,
            "message" => "Student deleted"
            ]);
        }else{
            return $this->respond([
                "status" => false,
                "message" => "Failed to delete data"
            ]);
        }
        }else{
        return $this->respond([
            "status" => false,
            "message" => "Student not found"
        ]);
    }
}
    // Check student by email
    private function checkStudentByEmail($email)
    {
        return $this->model->where("email", $email)->first();
    }
}