<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payroll extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        // Load thư viện cần thiết
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);

        // Load các model cần thiết
        $this->load->model('login_model');
        $this->load->model('dashboard_model');
        $this->load->model('employee_model');
        $this->load->model('leave_model');
        $this->load->model('payroll_model');
        $this->load->model('settings_model');
        $this->load->model('organization_model');
        $this->load->model('loan_model');
    }

    public function index()
    {
        if ($this->session->userdata('user_login_access') == 1) {
            redirect('dashboard/Dashboard');
        }
        $this->load->view('login');
    }

    public function Salary_Type()
    {
        if ($this->session->userdata('user_login_access') != False) {
            $data['typevalue'] = $this->payroll_model->GetsalaryType();
            $this->load->view('backend/salary_type', $data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function Salary_List()
    {
        if ($this->session->userdata('user_login_access') != False) {
            $data['salary_info'] = $this->payroll_model->getAllSalaryData();
            $this->load->view('backend/salary_list', $data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    // Tạo Phiếu Lương
    public function Generate_salary()
    {
        if ($this->session->userdata('user_login_access') != False) {
            $data['typevalue'] = $this->payroll_model->GetsalaryType();
            $data['employee'] = $this->employee_model->emselect();
            $data['salaryvalue'] = $this->payroll_model->GetAllSalary();
            $data['department'] = $this->organization_model->depselect();
            $this->load->view('backend/salary_view', $data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    // Báo Cáo Bảng Lương
    public function Payslip_Report()
    {
        if ($this->session->userdata('user_login_access') != False) {
            $data = [];
            $data['employee'] = $this->employee_model->emselect();
            $this->load->view('backend/salary_report', $data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function Invoice()
    {
        if ($this->session->userdata('user_login_access') != False) {
            $id = $this->input->get('Id');
            $eid = $this->input->get('em');

            $data['salary_info'] = $this->payroll_model->getAllSalaryDataById($id);
            $data['employee_info'] = $this->payroll_model->getEmployeeID($eid);
            $data['salaryvalue'] = $this->payroll_model->GetsalaryValueByID($eid);
            $data['loanvaluebyid'] = $this->payroll_model->GetLoanValueByID($eid);
            $data['settingsvalue'] = $this->settings_model->GetSettingsValue();

            if ($data['salaryvalue']) {
                $data['addition'] = $this->payroll_model->getAdditionDataBySalaryID($data['salaryvalue']->id);
                $data['diduction'] = $this->payroll_model->getDiductionDataBySalaryID($data['salaryvalue']->id);
            } else {
                $data['addition'] = 0;
                $data['diduction'] = 0;
            }

            if (!empty($data['salary_info'])) {
                $month_init = $data['salary_info']->month;
            } else {
                $month_init = date('Y-m-01');
            }

            if (!empty($data['employee_info'])) {
                $id_em = $data['employee_info']->em_id;
            } else {
                echo "<h3>Lỗi: Không tìm thấy nhân viên!</h3>";
                exit;
            }

            $data['id_em'] = $id_em;
            $data['month'] = date("n", strtotime($month_init));

            $employee_salary = $this->payroll_model->GetsalaryValueByID($id_em);
            $data['employee_salary'] = $employee_salary ? $employee_salary->total : 0;

            $this->load->view('backend/invoice', $data);
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    private function count_friday($month, $year)
    {
        $fridays = 0;
        $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($i = 1; $i <= $total_days; $i++) {
            if (date('N', strtotime($year . '-' . $month . '-' . $i)) == 5) {
                $fridays++;
            }
        }
        return $fridays;
    }

    private function total_days_in_a_month($month, $year)
    {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }
}