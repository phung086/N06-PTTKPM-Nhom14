<?php $this->load->view('backend/header'); ?>
<?php $this->load->view('backend/sidebar'); ?>
<div class="page-wrapper">
    <div class="message"></div>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                <i class="fa fa-calendar-check-o" style="color:#1976d2"></i> Chấm Công
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Chấm Công</li>
            </ol>
        </div>
    </div>

    <!-- Container fluid  -->
    <div class="container-fluid">
        <div class="row m-b-10">
            <div class="col-12">
                <button type="button" class="btn btn-info">
                    <i class="fa fa-plus"></i>
                    <a href="<?php echo base_url(); ?>attendance/Save_Attendance" class="text-white"> Thêm </a>
                </button>
                <button type="button" class="btn btn-primary">
                    <i class="fa fa-plus"></i>
                    <a href="#" class="text-white" data-toggle="modal" data-target="#Bulkmodal"> Thêm Hàng Loạt</a>
                </button>
                <button type="button" class="btn btn-info">
                    <i class="fa fa-plus"></i>
                    <a href="<?php echo base_url(); ?>attendance/Attendance_Report" class="text-white"> Báo Cáo Chấm
                        Công </a>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="m-b-0 text-white"> Danh Sách Chấm Công </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="attendance123"
                                class="display nowrap table table-hover table-striped table-bordered" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Mã Nhân Viên</th>
                                        <th>Thời Gian</th>
                                        <th>Checkin</th>
                                        <th>CheckOut</th>
                                        <th>Thời Gian Làm Việc</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendancelist as $value): ?>
                                    <tr>
                                        <td><mark>
                                                <?php
                                                    echo (!empty($value->first_name) && !empty($value->last_name)) ?
                                                        $value->first_name . ' ' . $value->last_name : "Không có tên";
                                                    ?>
                                            </mark></td>
                                        <td><?php echo !empty($value->emp_id) ? $value->emp_id : "N/A"; ?></td>
                                        <td><?php echo !empty($value->atten_date) ? $value->atten_date : "N/A"; ?></td>
                                        <td><?php echo !empty($value->signin_time) ? $value->signin_time : "N/A"; ?>
                                        </td>
                                        <td><?php echo !empty($value->signout_time) ? $value->signout_time : "N/A"; ?>
                                        </td>
                                        <td><?php echo !empty($value->working_hour) ? $value->working_hour : "N/A"; ?>
                                        </td>
                                        <td class="jsgrid-align-center">
                                            <?php if ($value->signout_time == '00:00:00') { ?>
                                            <a href="Save_Attendance?A=<?php echo $value->id; ?>" title="Sửa"
                                                class="btn btn-sm btn-danger waves-effect waves-light"> Đăng Xuất
                                            </a><br>
                                            <?php } ?>
                                            <a href="Save_Attendance?A=<?php echo $value->id; ?>" title="Sửa"
                                                class="btn btn-sm btn-primary waves-effect waves-light">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal thêm chấm công hàng loạt -->
        <div id="Bulkmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="import" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Thêm Chấm Công</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <h4>Nhập Chấm Công <span><img src="<?php echo base_url(); ?>assets/images/finger.jpg"
                                        height="100px" width="100px"></span> Upload only CSV file</h4>
                            <input type="file" name="csv_file" id="csv_file" accept=".csv"><br><br>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success waves-effect">Lưu</button>
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Đóng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- End Container-fluid -->
</div> <!-- End Page-wrapper -->

<?php $this->load->view('backend/footer'); ?>

<!-- DataTable Script -->
<script>
$('#attendance123').DataTable({
    "aaSorting": [
        [2, 'desc']
    ],
    dom: 'Bfrtip',
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ]
});
</script>