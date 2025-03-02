<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attendance extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('login_model');
        $this->load->model('dashboard_model');
        $this->load->model('employee_model');
        $this->load->model('loan_model');
        $this->load->model('settings_model');
        $this->load->model('leave_model');
        $this->load->model('attendance_model');
        $this->load->model('project_model');
        $this->load->library('csvimport');
    }

    public function Attendance()
    {
        if ($this->session->userdata('user_login_access') != false) {
            $data['attendancelist'] = $this->attendance_model->getAllAttendance();
            // Đổi tên hàm
            $this->load->view('backend/attendance', $data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function Save_Attendance()
    {
        if ($this->session->userdata('user_login_access') != false) {
            $data['employee'] = $this->employee_model->emselect();
            $id = $this->input->get('A');
            if (!empty($id)) {
                $data['attval'] = $this->attendance_model->em_attendanceFor($id);
            }
            $this->load->view('backend/add_attendance', $data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function Attendance_Report()
    {
        if ($this->session->userdata('user_login_access') != false) {
            $data['employee'] = $this->employee_model->emselect();
            $id = $this->input->get('A');
            if (!empty($id)) {
                $data['attval'] = $this->attendance_model->em_attendanceFor($id);
            }
            $this->load->view('backend/attendance_report', $data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function getPINFromID($employee_ID)
    {
        return $this->attendance_model->getPINFromID($employee_ID);
    }

    public function Get_attendance_data_for_report()
    {
        if ($this->session->userdata('user_login_access') != false) {
            $date_from = $this->input->post('date_from');
            $date_to = $this->input->post('date_to');
            $employee_id = $this->input->post('employee_id');
            $employee_PIN = $this->getPINFromID($employee_id)->em_code;
            $attendance_data = $this->attendance_model->getAttendanceDataByID($employee_PIN, $date_from, $date_to);

            if (!empty($attendance_data)) {
                $data['attendance'] = $attendance_data;
                $attendance_hours = $this->attendance_model->getTotalAttendanceDataByID($employee_PIN, $date_from, $date_to);
                $data['name'] = $attendance_data[0]->name;
                $data['days'] = count($attendance_data);
                $data['hours'] = $attendance_hours;
                echo json_encode($data);
            } else {
                echo json_encode(["error" => "No data found"]);
            }
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function Add_Attendance()
    {
        if ($this->session->userdata('user_login_access') != false) {
            $id = $this->input->post('id');
            $em_id = $this->input->post('emid');
            $attdate = $this->input->post('attdate');
            $signin = $this->input->post('signin');
            $signout = $this->input->post('signout');
            $place = $this->input->post('place');

            $this->form_validation->set_rules('attdate', 'Date details', 'trim|required|xss_clean');
            $this->form_validation->set_rules('emid', 'Employee', 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                echo validation_errors();
            } else {
                $new_date_changed = date('Y-m-d', strtotime($attdate));
                $sin = new DateTime($new_date_changed . ' ' . $signin);
                $sout = new DateTime($new_date_changed . ' ' . $signout);
                $work = $sin->diff($sout)->format('%H h %i m');

                if (empty($id)) {
                    $duplicate = $this->attendance_model->getDuplicateVal($em_id, $new_date_changed);
                    if (!empty($duplicate)) {
                        echo "Đã tồn tại";
                    } else {
                        $data = [
                            'emp_id' => $em_id,
                            'atten_date' => $new_date_changed,
                            'signin_time' => $signin,
                            'signout_time' => $signout,
                            'working_hour' => $work,
                            'place' => $place,
                            'status' => 'A'
                        ];
                        $this->attendance_model->Add_AttendanceData($data);
                        echo "Thêm thành công.";
                    }
                } else {
                    $data = [
                        'signin_time' => $signin,
                        'signout_time' => $signout,
                        'working_hour' => $work,
                        'place' => $place,
                        'status' => 'A'
                    ];
                    $this->attendance_model->Update_AttendanceData($id, $data);
                    echo "Cập nhật thành công.";
                }
            }
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function import()
    {
        $this->load->library('csvimport');
        $file_data = $this->csvimport->get_array($_FILES["csv_file"]["tmp_name"]);
        foreach ($file_data as $row) {
            if ($row["Check-in at"] > '0:00:00') {
                $date = date('Y-m-d', strtotime($row["Date"]));
                $duplicate = $this->attendance_model->getDuplicateVal($row["Employee No"], $date);

                $data = [
                    'emp_id' => $row["Employee No"],
                    'atten_date' => $date,
                    'signin_time' => $row["Check-in at"],
                    'signout_time' => $row["Check-out at"],
                    'working_hour' => $row["Work Duration"],
                    'absence' => $row["Absence Duration"],
                    'overtime' => $row["Overtime Duration"],
                    'status' => 'A',
                    'place' => 'office'
                ];

                if (!empty($duplicate)) {
                    $this->attendance_model->bulk_Update($row["Employee No"], $date, $data);
                } else {
                    $this->attendance_model->Add_AttendanceData($data);
                }
            }
        }
        echo "Cập nhật thành công";
    }
}
?>