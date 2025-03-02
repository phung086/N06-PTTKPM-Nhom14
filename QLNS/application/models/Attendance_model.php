<?php
class Attendance_model extends CI_Model
{
  function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function Add_AttendanceData($data)
  {
    return $this->db->insert('attendance', $data);
  }

  public function bulk_insert($data)
  {
    return $this->db->insert_batch('attendance', $data);
  }

  public function em_attendance()
  {
    $sql = "SELECT a.*, 
                   e.em_id, e.em_code, 
                   COALESCE(e.first_name, 'Không có tên') AS first_name, 
                   COALESCE(e.last_name, '') AS last_name
            FROM attendance AS a
            LEFT JOIN employee AS e ON a.emp_id = e.em_code
            WHERE a.status = 'A'
            ORDER BY a.id DESC";

    $query = $this->db->query($sql);
    return $query->result();
  }

  public function em_attendanceFor($id)
  {
    $sql = "SELECT a.*, 
                   e.em_id, e.em_code, 
                   COALESCE(e.first_name, 'Không có tên') AS first_name, 
                   COALESCE(e.last_name, '') AS last_name
            FROM attendance AS a
            LEFT JOIN employee AS e ON a.emp_id = e.em_code
            WHERE a.id = ?";

    $query = $this->db->query($sql, array($id));
    return $query->row();
  }

  public function Update_AttendanceData($id, $data)
  {
    $this->db->where('id', $id);
    return $this->db->update('attendance', $data);
  }

  public function getAttendanceDataByID($employee_id, $date_from, $date_to)
  {
    $sql = "SELECT a.*, 
                   e.em_id, e.em_code, 
                   CONCAT(COALESCE(e.first_name, 'Không có tên'), ' ', COALESCE(e.last_name, '')) AS name,
                   TIMEDIFF(a.signout_time, a.signin_time) AS Hours
            FROM attendance AS a
            LEFT JOIN employee AS e ON a.emp_id = e.em_code
            WHERE e.em_id = ?
            AND a.atten_date BETWEEN ? AND ?
            AND a.status = 'A'";

    $query = $this->db->query($sql, array($employee_id, $date_from, $date_to));
    return $query->result();
  }


  public function getAllAttendance()
  {
    $sql = "SELECT a.id, a.emp_id, a.atten_date, a.signin_time, a.signout_time, 
                   TIMEDIFF(a.signout_time, a.signin_time) AS work_hours,
                   COALESCE(e.first_name, 'Không có tên') AS first_name, 
                   COALESCE(e.last_name, '') AS last_name
            FROM attendance AS a
            LEFT JOIN employee AS e ON a.emp_id = e.em_code
            WHERE a.status = 'A'";

    $query = $this->db->query($sql);
    return $query->result();
  }

  public function getPINFromID($employee_ID)
  {
    $sql = "SELECT em_id FROM employee WHERE em_id = ?";
    $query = $this->db->query($sql, array($employee_ID));
    return $query->row();
  }

  public function getDuplicateVal($emid, $date)
  {
    $sql = "SELECT * FROM attendance WHERE emp_id = ? AND atten_date = ?";
    $query = $this->db->query($sql, array($emid, $date));
    return $query->row();
  }
}
?>