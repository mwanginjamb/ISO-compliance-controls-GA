<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ISO 27001:2022 Gap Analysis Dashboard</title>


</head>

<body>
    <div class="content-wrap">
        <!-- Header -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="https://via.placeholder.com/30" alt="Logo" class="mr-2">
                    <strong>MyCompany</strong>
                </a>
                <div class="ml-auto">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileMenu"
                                data-toggle="dropdown">
                                <img src="https://via.placeholder.com/30" class="rounded-circle mr-2" alt="User">
                                <span>Admin</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileMenu">
                                <a class="dropdown-item" href="#">Profile</a>
                                <a class="dropdown-item" href="#">Settings</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-2 sidebar">
                    <div class="px-3">
                        <h5>ISO 27001:2022</h5>
                        <small class="text-muted">Gap Analysis Portal</small>
                    </div>
                    <hr>
                    <ul class="nav flex-column px-3">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Clause Details</a>
                        </li>
                    </ul>

                    <div class="px-3 mt-4 d-flex justify-content-between align-items-center">
                        <h6 class="text-muted mb-0">Assessments</h6>
                        <button class="btn btn-sm btn-primary">+ New</button>
                    </div>

                    <!-- Assessment card -->
                    <div class="card assessment-card mx-3 mt-2 p-2">
                        <small class="font-weight-bold">Initial Gap Analysis 2024</small>
                        <small class="text-muted d-block">Sample Organization</small>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">0% complete</small>
                            <small class="text-primary font-weight-bold">0% compliant</small>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-10 p-4">
                    <h4>ISO 27001:2022 Gap Analysis Dashboard</h4>

                    <!-- Top Stats -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h5>0%</h5>
                                <p class="text-muted mb-1">Overall Compliance</p>
                                <div class="progress">
                                    <div class="progress-bar bg-info" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h5 class="text-success">0%</h5>
                                <p class="text-muted mb-1">Assessment Progress</p>
                                <small class="text-muted">0 of 44 clauses</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card p-3 text-danger">
                                <h5>0</h5>
                                <p class="mb-0">Non-Compliant</p>
                                <small>Requires immediate attention</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card p-3 text-warning">
                                <h5>0</h5>
                                <p class="mb-0">Partially Compliant</p>
                                <small>Needs improvement</small>
                            </div>
                        </div>
                    </div>

                    <!-- Compliance Distribution and Gaps -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h6>Compliance Distribution</h6>
                                <div><span class="badge-dot bg-success"></span> Compliant - 0</div>
                                <div><span class="badge-dot bg-warning"></span> Partially Compliant - 0</div>
                                <div><span class="badge-dot bg-danger"></span> Non-Compliant - 0</div>
                                <div><span class="badge-dot bg-secondary"></span> Not Applicable - 0</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h6>Gaps by Priority</h6>
                                <div><span class="badge-dot bg-danger"></span> High Priority - 0</div>
                                <div><span class="badge-dot bg-warning"></span> Medium Priority - 0</div>
                                <div><span class="badge-dot bg-primary"></span> Low Priority - 0</div>
                            </div>
                        </div>
                    </div>

                    <!-- Compliance by Category -->
                    <div class="card p-3 mt-3">
                        <h6>Compliance by Category</h6>
                        <div class="row">
                            <!-- Sample category item (repeat or loop through categories) -->
                            <div class="col-md-3 mb-3">
                                <h6 class="text-primary">Improvement</h6>
                                <p>0%<br><small>0 of 3 assessed</small></p>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <h6 class="text-primary">Context</h6>
                                <p>0%<br><small>0 of 5 assessed</small></p>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <h6 class="text-primary">Leadership</h6>
                                <p>0%<br><small>0 of 4 assessed</small></p>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <h6 class="text-primary">Planning</h6>
                                <p>0%<br><small>0 of 4 assessed</small></p>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: 0%"></div>
                                </div>
                            </div>
                            <!-- Add the remaining categories similarly: Support, Operation, Technology, etc. -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Sticky Footer -->
    <footer class="footer text-center">
        &copy; <?= date('Y') ?> MyCompany — ISO 27001:2022 Gap Analysis Portal
    </footer>
</body>

</html>

<?php
$style = <<<CSS
html, body {
      height: 100%;
    }
body {
      background-color: #f4f6fa;
      font-size: 14px;
    }
      .content-wrap {
      flex: 1 0 auto;
    }

     .footer {
      flex-shrink: 0;
      background: #ffffff;
      border-top: 1px solid #dee2e6;
      padding: 10px 20px;
      font-size: 13px;
      color: #6c757d;
      box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.03);
    }
    .navbar {
      background-color: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.06);
       display: flex;
      flex-direction: column;
    }
    .sidebar {
      background-color: #f9fafc;
      min-height: 100vh;
      border-right: 1px solid #e0e0e0;
      padding-top: 1rem;
    }
    .sidebar h6 {
      font-size: 13px;
      color: #6c757d;
    }
    .nav-link {
      color: #495057;
      padding: 8px 12px;
    }
    .nav-link.active {
      background-color: #e3ebfc;
      color: #2c3e50;
      border-radius: 4px;
    }
    .card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.06);
      margin-bottom: 1rem;
    }
    .assessment-card {
      border-left: 4px solid #007bff;
      cursor: pointer;
    }
    .assessment-card:hover {
      background-color: #f0f5ff;
    }
    .badge-dot {
      height: 10px;
      width: 10px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 5px;
    }
    .sidebar .btn {
      font-size: 13px;
    }
    .progress {
      height: 6px;
    }

CSS;

$this->registerCss($style);
?>