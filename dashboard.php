<?php
// dashboard.php (inside SSC folder)
session_start(); // Ensure session is active
include "config.php";
if (!isset($_SESSION['email'])) {
    header("Location: admin/login.php");
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Get username and profile photo from session
$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "User";
$photo    = isset($_SESSION['user_photo']) && !empty($_SESSION['user_photo']) ? $_SESSION['user_photo'] : null;
$initial  = strtoupper(substr($username, 0, 1));

function courseAbbrev($course) {
    $map = [
        'Bachelor of Science in Information Technology' => 'BSIT',
        'Bachelor of Science in Computer Science' => 'BSCS',
        'Bachelor of Science in Computer Engineering' => 'BSCPE',
        'Bachelor of Science in Business Administration' => 'BSBA',
        'Bachelor of Science in Accountancy' => 'BSA',
        'Bachelor of Elementary Education' => 'BEED',
        'Bachelor of Secondary Education' => 'BSED',
        'Bachelor of Science in Hospitality Management' => 'BSHM',
        'Bachelor of Science in Tourism Management' => 'BSTM',
        'Bachelor of Science in Criminology' => 'BSCRIM',
    ];
    return $map[$course] ?? strtoupper($course); // fallback: all caps
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SSC Dashboard - Test Edit</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      background-color: #f5fdf5;
      font-family: "Poppins", sans-serif;
    }
    /* Hover scale for dashboard stat cards */
    .dashboard-stats .card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dashboard-stats .card:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      z-index: 1;
    }
    .sidebar {
      width: 250px;
      background: #2e7d32;
      color: white;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      padding-top: 60px;
      transition: transform 0.3s ease-in-out;
      z-index: 99;
    }
    .sidebar.hide {
      transform: translateX(-100%);
    }
    .sidebar a {
      display: block;
      padding: 15px 20px;
      text-decoration: none;
      color: white;
      font-size: 16px;
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }
    .sidebar a:hover {
      background: #43a047;
      border-left: 4px solid #c8e6c9;
      padding-left: 25px;
    }
    .sidebar a.active {
      background: #1b5e20;
      border-left: 4px solid #a5d6a7;
      font-weight: 600;
    }
    .header {
      position: fixed;
      top: 0; left: 0; right: 0;
      height: 60px;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      z-index: 1000;
    }
    .hamburger {
      font-size: 24px;
      cursor: pointer;
      color: #2e7d32;
    }
    .content {
      margin-left: 250px;
      padding: 80px 20px 20px;
      transition: margin-left 0.3s;
    }
    .sidebar.hide ~ .content {
      margin-left: 0;
    }
    .sidebar h4 {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      user-select: none;
    } 
    .student-table th, 
    .student-table td {
      text-align: center;
      vertical-align: middle;
      white-space: nowrap; /* prevent wrapping */
    }

    /* Adjust widths for each column */
    .student-table th:nth-child(1), .student-table td:nth-child(1) { width: 4%; }   /* No. */
    .student-table th:nth-child(2), .student-table td:nth-child(2) { width: 12%; }  /* Student ID */
    .student-table th:nth-child(3), .student-table td:nth-child(3) { width: 18%; }  /* Name */
    .student-table th:nth-child(4), .student-table td:nth-child(4) { width: 10%; }  /* Course */
    .student-table th:nth-child(5), .student-table td:nth-child(5) { width: 8%; }   /* Year */
    .student-table th:nth-child(6), .student-table td:nth-child(6) { width: 10%; }  /* Card No */
    .student-table th:nth-child(7), .student-table td:nth-child(7) { width: 8%; }   /* Points */
    .student-table th:nth-child(8), .student-table td:nth-child(8) { width: 12%; }  /* Last Activity */
    .student-table th:nth-child(9), .student-table td:nth-child(9) { width: 18%; }  /* Actions */

    @media(max-width: 768px){
      .sidebar {transform: translateX(-100%);}
      .sidebar.show {transform: translateX(0);}
      .content {margin-left: 0;}
    }
    
    /* Student Details Modal Styling */
    #studentDetailsModal .modal-dialog {
      max-width: 90vw;
      margin: 1rem auto;
    }
    
    #studentDetailsModal .modal-content {
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    #studentDetailsModal .modal-header {
      border-radius: 15px 15px 0 0;
      padding: 1.5rem;
    }
    
    #studentDetailsModal .card {
      border: none;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 10px;
    }
    
    #studentDetailsModal .card-header {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-radius: 10px 10px 0 0;
      border-bottom: 2px solid #dee2e6;
    }
    
    #modal-current-points {
      font-size: 3rem;
      font-weight: bold;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }
    
    .student-table tbody tr:hover {
      background-color: rgba(46, 125, 50, 0.1);
      transform: scale(1.01);
      transition: all 0.2s ease;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <span class="hamburger" onclick="toggleSidebar()"><i class="bi bi-list"></i></span>
    
    <!-- Immediate hamburger script -->
    <script>
      // Ensure hamburger works immediately
      document.addEventListener('click', function(e) {
        if (e.target.closest('.hamburger')) {
          e.preventDefault();
          const sidebar = document.getElementById('sidebar');
          if (sidebar) {
            if (window.innerWidth <= 768) {
              sidebar.classList.toggle('show');
            } else {
              sidebar.classList.toggle('hide');
            }
          }
        }
      });
    </script>

    <!-- Profile Dropdown -->
    <div class="dropdown ms-auto">
      <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php if ($photo): ?>
          <!-- Show profile photo -->
          <img src="uploads/<?= htmlspecialchars($photo) ?>" alt="Profile" class="rounded-circle me-2" style="width:40px; height:40px; object-fit:cover;">
        <?php else: ?>
          <!-- Show initial -->
          <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center me-2" 
               style="width:40px; height:40px; font-weight:bold;">
            <?= $initial ?>
          </div>
        <?php endif; ?>

        <span class="text-success fw-bold"><?= htmlspecialchars($username) ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow">
        <li><a class="dropdown-item" href="settings/edit_profile.php"><i class="bi bi-person"></i> Edit Profile</a></li>
        <li><a class="dropdown-item" href="settings/change_password.php"><i class="bi bi-lock"></i> Change Password</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="settings/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="d-flex align-items-center px-3 mb-3 mt-3">
      <img src="assests/img/S.png"
           class="rounded-circle mb-2 shadow"
           style="width:40px; height:40px; object-fit:contain; background:#fff; padding:1px; background-position: center;">
      <h4 class="text-white ms-2">SortPoint</h4>
      <hr class="border-light">
    </div>

    <a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="?page=students" class="<?= $page=='students'?'active':'' ?>"><i class="bi bi-people"></i> Student Points</a>
    <a href="?page=history" class="<?= $page=='history'?'active':'' ?>"><i class="bi bi-clock-history"></i> Transactions</a>
    <a href="?page=settings" class="<?= $page=='settings'?'active':'' ?>"><i class="bi bi-gear"></i> Settings</a>
  </div>

  <!-- Content -->
  <div class="content">
    <?php
      if($page == 'dashboard') {
        // Query live counts
        $totalStudentsRes = $conn->query("SELECT COUNT(*) AS total FROM student_tbl");
        $totalStudents = $totalStudentsRes ? (int)$totalStudentsRes->fetch_assoc()['total'] : 0;
        
        // Query total points
        $totalPointsRes = $conn->query("SELECT COALESCE(SUM(points), 0) AS total FROM student_tbl");
        $totalPoints = $totalPointsRes ? (float)$totalPointsRes->fetch_assoc()['total'] : 0;
        
        // Query course distribution for pie chart
        $courseStatsRes = $conn->query("SELECT course, COUNT(*) as count, SUM(points) as total_points FROM student_tbl GROUP BY course ORDER BY count DESC");
        $courseStats = [];
        $courseLabels = [];
        $courseData = [];
        $courseColors = ['#2e7d32', '#43a047', '#66bb6a', '#81c784', '#a5d6a7', '#c8e6c9'];
        $colorIndex = 0;
        
        if ($courseStatsRes instanceof mysqli_result) {
          while($row = $courseStatsRes->fetch_assoc()) {
              $courseStats[] = $row;
              $courseLabels[] = courseAbbrev($row['course']);
              $courseData[] = (int)$row['count'];
              $colorIndex++;
          }
        }
        
        // Total redeemed transactions count (for third box)
        $redeemCountRes = $conn->query("SELECT COUNT(*) AS c FROM point_transactions WHERE source='REDEEM' OR points_added < 0");
        $totalRedeemedTx = $redeemCountRes ? (int)$redeemCountRes->fetch_assoc()['c'] : 0;
        echo '
        <div class="container-fluid">
          <div class="row g-3 mb-4 dashboard-stats">
            <div class="col-md-4">
              <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center">
                  <i class="bi bi-people fs-1"></i>
                  <h2 class="fw-bold">'.number_format($totalStudents).'</h2>
                  <p class="mb-0">Total Students</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center">
                  <i class="bi bi-star fs-1"></i>
                  <h2 class="fw-bold">'.number_format($totalPoints, 1).'</h2>
                  <p class="mb-0">Total Points</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center">
                  <i class="bi bi-receipt fs-1"></i>
                  <h2 class="fw-bold">'.number_format($totalRedeemedTx).'</h2>
                  <p class="mb-0">Total Redeem Transactions</p>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-success"><i class="bi bi-bar-chart-line"></i> Reports & Statistics</h5>
              <p>Overview of recent activities and performance.</p>
              
              <div class="row g-4">
                <!-- Points Distribution Chart -->
                <div class="col-md-6">
                  <div class="card h-100">
                    <div class="card-header bg-light">
                      <h6 class="mb-0"><i class="bi bi-pie-chart"></i> Points Distribution by Course</h6>
                    </div>
                    <div class="card-body">
                      <canvas id="pointsDistributionChart" height="300"></canvas>
                    </div>
                  </div>
                </div>
                
                <!-- Student Activity Chart -->
                <div class="col-md-6">
                  <div class="card h-100">
                    <div class="card-header bg-light">
                      <h6 class="mb-0"><i class="bi bi-graph-up"></i> Student Activity (Last 7 Days)</h6>
                    </div>
                    <div class="card-body">
                      <div style="position: relative; height:300px;">
                        <canvas id="activityChart"></canvas>
                      </div>
                      <div id="activityChartDate" class="text-muted mt-1"></div>
                    </div>
                  </div>
                </div>
                
                <!-- Top Performers -->
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header bg-light">
                      <h6 class="mb-0"><i class="bi bi-trophy"></i> Top Performers</h6>
                    </div>
                    <div class="card-body">
                      <canvas id="topPerformersChart" height="200"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>'; 
      } elseif($page == 'students') {

        $courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
        $courseQuery = $conn->query("SELECT DISTINCT course FROM student_tbl ORDER BY course ASC");
        $courses = [];
        while($c = $courseQuery->fetch_assoc()) {
            $courses[] = $c['course'];
        }

        // ✅ Pagination setup
        $limit = 10;
        $pageNum = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($pageNum < 1) $pageNum = 1;
        $offset = ($pageNum - 1) * $limit;

        // Build WHERE clause for course filtering
        $whereClause = '';
        if (!empty($courseFilter)) {
            $escapedCourse = mysqli_real_escape_string($conn, $courseFilter);
            $whereClause = "WHERE course = '$escapedCourse'";
        }

        // Count total with course filter
        $countQuery = "SELECT COUNT(*) AS total FROM student_tbl $whereClause";
        $countRes = $conn->query($countQuery);
        $totalRows = $countRes->fetch_assoc()['total'];
        $totalPages = ceil($totalRows / $limit);

        // Fetch students with course filter
        $studentQuery = "SELECT * FROM student_tbl $whereClause ORDER BY last_name ASC LIMIT $limit OFFSET $offset";
        $result = $conn->query($studentQuery);
        $num = $offset + 1;
        ?>
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="text-success"><i class="bi bi-people"></i> Student Points</h5>
            
           <?php if (!empty($courseFilter)): ?>
            <div class="alert alert-success mb-2 py-3 px-3" style="font-size: 1rem;">
              <i class="bi bi-person"></i> Showing students from: 
              <strong><?= htmlspecialchars(courseAbbrev($courseFilter)) ?></strong> 
              (<?= $totalRows ?> student<?= $totalRows != 1 ? 's' : '' ?>)
              <a href="?page=students" class="btn btn-sm btn-outline-success ms-2 py-0 px-2" style="font-size: 0.938rem;">Show All</a>
            </div>
          <?php else: ?>
            <div class="text-muted mb-2" style="font-size: 1rem;">
              <i class="bi bi-info-circle"></i> Showing all students (<?= $totalRows ?> total)
            </div>
          <?php endif; ?>


            <!-- Course Dropdown -->
          <form method="GET" class="mb-3 d-flex align-items-center">
            <input type="hidden" name="page" value="students">
            <label class="me-2 fw-bold">Filter by Course:</label>
            <select name="course" class="form-select w-auto me-2" onchange="this.form.submit()">
              <option value="">All Courses</option>
              <?php foreach($courses as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $courseFilter==$c?'selected':'' ?>>
                  <?= htmlspecialchars(courseAbbrev($c)) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </form>

            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createStudentModal">
              <i class="bi bi-plus-circle"></i> Add Student
            </button>

            <!-- Student Table -->
            <div class="table-responsive">
            <table class="table table-bordered align-middle student-table">
              <thead class="table-success">
                <tr>
                  <th>No.</th>
                  <th>Student ID</th>
                  <th>Name</th>
                  <th>Course</th>
                  <th>Year</th>
                  <th>Card No</th>
                  <th>Points</th>
                  <th>Last Activity</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                while($row = $result->fetch_assoc()): 
                  $middleInitial = !empty($row['middle_name']) ? strtoupper(substr($row['middle_name'], 0, 1)) . "." : "";
                ?>
                  <tr style="cursor: pointer;" onclick="openStudentModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['student_id']) ?>', '<?= htmlspecialchars($row['first_name']) ?>', '<?= htmlspecialchars($row['middle_name']) ?>', '<?= htmlspecialchars($row['last_name']) ?>', '<?= htmlspecialchars($row['course']) ?>', '<?= htmlspecialchars($row['year_level']) ?>', '<?= htmlspecialchars($row['card_no']) ?>', <?= $row['points'] ?? 0 ?>)">
                    <td><?= $num++ ?></td>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['last_name'] . ", " . $row['first_name'] . " " . $middleInitial) ?></td>
                    <td><?= htmlspecialchars(courseAbbrev($row['course'])) ?></td>
                    <td><?= htmlspecialchars($row['year_level']) ?></td>
                    <td><?= htmlspecialchars($row['card_no']) ?></td>
                    <td>
                      <span class="points-display badge bg-success" style="font-size: 0.9rem; padding: 0.6em 0.9em;" id="points-<?= $row['id'] ?>">
                        <?= number_format($row['points'] ?? 0, 2) ?>
                      </span>

                      <!-- <small class="text-muted d-block">RFID Auto-Add</small> -->
                    </td>
                    <td>
                      <small class="text-muted" id="activity-<?= $row['id'] ?>">
                        <?php if (!empty($row['last_activity'])): ?>
                          <?= date('M j, Y', strtotime($row['last_activity'])) ?><br>
                          <span class="text-success"><?= date('g:i A', strtotime($row['last_activity'])) ?></span>
                        <?php else: ?>
                          <span class="text-muted">No activity</span>
                        <?php endif; ?>
                      </small>
                    </td>
                    <td>
                      <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editStudentModal<?= $row['id'] ?>" onclick="event.stopPropagation()"> Edit
                      </button>
                      <a href="student/delete_stud.php?id=<?= $row['id'] ?>" 
                        class="btn btn-danger btn-sm" onclick="return confirm('Delete this student?')"> Delete
                      </a>
                    </td>
                  </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editStudentModal<?= $row['id'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <form action="student/edit_stud.php" method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit Student</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row g-2">
                                <div class="col-md-4">
                                  <label>First Name</label>
                                  <input type="text" name="first_name" value="<?= $row['first_name'] ?>" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                  <label>Middle Name</label>
                                  <input type="text" name="middle_name" value="<?= $row['middle_name'] ?>" class="form-control">
                                </div>
                                <div class="col-md-4">
                                  <label>Last Name</label>
                                  <input type="text" name="last_name" value="<?= $row['last_name'] ?>" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                  <label>Student ID</label>
                                  <input type="text" name="student_id" value="<?= $row['student_id'] ?>" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                  <label>Course</label>
                                  <input type="text" name="course" value="<?= $row['course'] ?>" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                  <label>Year Level</label>
                                  <select name="year_level" class="form-select" required>
                                    <option <?= $row['year_level']=="1st Year"?"selected":"" ?>>1st Year</option>
                                    <option <?= $row['year_level']=="2nd Year"?"selected":"" ?>>2nd Year</option>
                                    <option <?= $row['year_level']=="3rd Year"?"selected":"" ?>>3rd Year</option>
                                    <option <?= $row['year_level']=="4th Year"?"selected":"" ?>>4th Year</option>
                                  </select>
                                </div>
                                <div class="col-md-6">
                                  <label>Card No</label>
                                  <input type="text" name="card_no" value="<?= $row['card_no'] ?>" class="form-control" required>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" name="edit" class="btn btn-success">Save Changes</button>
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>

            <!-- ✅ Pagination -->
            <?php if ($totalPages > 1): ?>
              <nav>
                <ul class="pagination justify-content-center">
                  <li class="page-item <?= $pageNum <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=students&p=<?= $pageNum-1 ?><?= !empty($courseFilter) ? '&course=' . urlencode($courseFilter) : '' ?>">Previous</a>
                  </li>
                  <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $pageNum == $i ? 'active' : '' ?>">
                      <a class="page-link" href="?page=students&p=<?= $i ?><?= !empty($courseFilter) ? '&course=' . urlencode($courseFilter) : '' ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>
                  <li class="page-item <?= $pageNum >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=students&p=<?= $pageNum+1 ?><?= !empty($courseFilter) ? '&course=' . urlencode($courseFilter) : '' ?>">Next</a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>

          </div>
        </div>

        <!-- Create Modal -->
        <div class="modal fade" id="createStudentModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <?php $formData = $_SESSION['form_data'] ?? []; ?>

            <div class="modal-content">

              <form id="createStudentForm" action="student/create_stud.php" method="POST">
                <div class="modal-header">
                  <h5 class="modal-title">Add Student</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="clearFormAndClose()"></button>
                </div>
                <div class="modal-body">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <label>First Name</label>
                      <input type="text" name="first_name" id="first_name" class="form-control" value="<?= htmlspecialchars($formData['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                      <label>Middle Name</label>
                      <input type="text" name="middle_name" id="middle_name" class="form-control" value="<?= htmlspecialchars($formData['middle_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                      <label>Last Name</label>
                      <input type="text" name="last_name" id="last_name" class="form-control" value="<?= htmlspecialchars($formData['last_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                      <label>Student ID</label>
                      <input type="text" name="student_id" id="student_id" class="form-control" value="<?= htmlspecialchars($formData['student_id'] ?? '') ?>" required>
                      <?php if (isset($_SESSION['modal_error']) && strpos($_SESSION['modal_error'], 'Student ID') !== false): ?>
                        <div class="text-danger mt-1" id="studentIdError" style="font-size: 0.875rem;">
                          <i class="bi bi-exclamation-circle"></i> <?= $_SESSION['modal_error']; ?>
                        </div>
                        <?php unset($_SESSION['modal_error']); ?>
                      <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                      <label>Course</label>
                      <input type="text" name="course" id="course" class="form-control" value="<?= htmlspecialchars($formData['course'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                      <label>Year Level</label>
                      <select name="year_level" id="year_level" class="form-select" required>
                        <option value="1st Year" <?= (isset($formData['year_level']) && $formData['year_level'] == '1st Year') ? 'selected' : '' ?>>1st Year</option>
                        <option value="2nd Year" <?= (isset($formData['year_level']) && $formData['year_level'] == '2nd Year') ? 'selected' : '' ?>>2nd Year</option>
                        <option value="3rd Year" <?= (isset($formData['year_level']) && $formData['year_level'] == '3rd Year') ? 'selected' : '' ?>>3rd Year</option>
                        <option value="4th Year" <?= (isset($formData['year_level']) && $formData['year_level'] == '4th Year') ? 'selected' : '' ?>>4th Year</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label>Card No</label>
                      <input type="text" name="card_no" id="card_no" class="form-control" value="<?= htmlspecialchars($formData['card_no'] ?? '') ?>" required>
                      <?php if (isset($_SESSION['modal_error']) && strpos($_SESSION['modal_error'], 'Card Number') !== false): ?>
                        <div class="text-danger mt-1" id="cardError" style="font-size: 0.875rem;">
                          <i class="bi bi-exclamation-circle"></i> <?= $_SESSION['modal_error']; ?>
                        </div>
                        <?php unset($_SESSION['modal_error']); ?>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" name="save" class="btn btn-success">Save</button>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="clearFormAndClose()">Cancel</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Student Details Modal -->
        <div class="modal fade" id="studentDetailsModal" tabindex="-1">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                  <i class="bi bi-person-circle"></i> Student Details & Points Management
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="closeStudentModal()"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <!-- Left Side - Student Information -->
                  <div class="col-md-6">
                    <div class="card h-100">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Student Information</h6>
                      </div>
                      <div class="card-body">
                        <div class="row mb-3">
                          <div class="col-sm-4"><strong>Student ID:</strong></div>
                          <div class="col-sm-8" id="modal-student-id">-</div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-4"><strong>Name:</strong></div>
                          <div class="col-sm-8" id="modal-student-name">-</div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-4"><strong>Course:</strong></div>
                          <div class="col-sm-8" id="modal-student-course">-</div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-4"><strong>Year Level:</strong></div>
                          <div class="col-sm-8" id="modal-student-year">-</div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-4"><strong>Card Number:</strong></div>
                          <div class="col-sm-8" id="modal-student-card">-</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Right Side - Points Management -->
                  <div class="col-md-6">
                    <div class="card h-100">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-star"></i> Points Management</h6>
                      </div>
                      <div class="card-body text-center">
                        <div class="mb-4">
                          <h2 class="text-success" id="modal-current-points">0</h2>
                          <p class="text-muted">Current Points</p>
                        </div>
                        
                        <div class="mb-4">
                          <label for="redeemPoints" class="form-label">Points to Redeem</label>
                          <input type="number" class="form-control form-control-lg text-center" id="redeemPoints" 
                                 min="0" step="0.1" placeholder="Enter points to redeem">
                        </div>
                        
                        <div class="d-grid gap-2">
                          <button type="button" class="btn btn-success btn-lg" onclick="redeemPoints()">
                            <i class="bi bi-gift"></i> Redeem Points
                          </button>
                          <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal" onclick="closeStudentModal()"> Cancel
                          </button>
                        </div>
                        
                        <div class="mt-3">
                          <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Points are automatically added via RFID scanning
                          </small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php
      } elseif($page == 'history') {
        // Course dropdown similar to student points
        $courseFilter = isset($_GET['course']) ? $_GET['course'] : '';
        $courseQuery = $conn->query("SELECT DISTINCT course FROM student_tbl ORDER BY course ASC");
        $courses = [];
        if ($courseQuery instanceof mysqli_result) {
          while($c = $courseQuery->fetch_assoc()) { $courses[] = $c['course']; }
        }

        // Build WHERE for history (redeemed only)
        $where = " WHERE (t.source = 'REDEEM' OR t.points_added < 0) ";
        if (!empty($courseFilter)) {
          $escapedCourse = mysqli_real_escape_string($conn, $courseFilter);
          $where .= " AND s.course = '".$escapedCourse."' ";
        }

        // Pagination for history
        $perPage = 10;
        $pageNum = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($pageNum < 1) $pageNum = 1;
        $offset = ($pageNum - 1) * $perPage;

        // Count total redeemed rows
        $countSql = "SELECT COUNT(*) AS total FROM point_transactions t LEFT JOIN student_tbl s ON s.id = t.student_id $where";
        $totalRows = 0;
        if ($cntRes = $conn->query($countSql)) { $totalRows = (int)$cntRes->fetch_assoc()['total']; }
        $totalPages = max(1, (int)ceil($totalRows / $perPage));

        // Query transactions joined with student, latest first with pagination
        $sqlHistory = "SELECT t.id, t.student_id, t.points_added, t.source, t.balance_after, t.created_at,
                              s.student_id AS stud_no, s.first_name, s.middle_name, s.last_name, s.course, s.points AS current_points
                       FROM point_transactions t
                       LEFT JOIN student_tbl s ON s.id = t.student_id
                       $where
                       ORDER BY t.created_at DESC
                       LIMIT $perPage OFFSET $offset";
        $txRes = $conn->query($sqlHistory);

        echo '<div class="card shadow-sm">'
            .'<div class="card-body">'
            .'<h5 class="text-success"><i class="bi bi-clock-history"></i> Redeemed Points</h5>';

        // Filter form
        echo '<form method="GET" class="mb-3 d-flex align-items-center">'
            .'<input type="hidden" name="page" value="history">'
            .'<label class="me-2 fw-bold">Filter by Course:</label>'
            .'<select name="course" class="form-select w-auto me-2" onchange="this.form.submit()">'
            .'<option value="">All Courses</option>';
        foreach($courses as $c) {
          $sel = ($courseFilter==$c)?'selected':'';
          echo '<option value="'.htmlspecialchars($c).'" '.$sel.'>'.htmlspecialchars(courseAbbrev($c)).'</option>';
        }
        echo '</select>'
            .'</form>';

        echo '<div class="table-responsive mt-2">'
            .'<table class="table table-bordered align-middle">'
            .'<thead class="table-success">'
            .'<tr>'
            .'<th>#</th>'
            .'<th>Student ID</th>'
            .'<th>Full Name</th>'
            .'<th>Course</th>'
            .'<th>Recent Points</th>'
            .'<th>Redeemed</th>'
            .'<th>Date</th>'
            .'<th>Time</th>'
            .'<th>Actions</th>'
            .'</tr>'
            .'</thead>'
            .'<tbody>';

        if ($txRes instanceof mysqli_result) {
          $n = 1;
          while ($tx = $txRes->fetch_assoc()) {
            $studNo    = htmlspecialchars($tx['stud_no'] ?? '');
            $mi        = isset($tx['middle_name']) && $tx['middle_name']!=='' ? (strtoupper(substr($tx['middle_name'],0,1)).'.') : '';
            $fullName  = htmlspecialchars(trim(($tx['last_name'] ?? '').', '.($tx['first_name'] ?? '').' '.$mi));
            $courseAb  = htmlspecialchars(courseAbbrev($tx['course'] ?? ''));
            $current   = isset($tx['balance_after']) && $tx['balance_after']!==null ? number_format((float)$tx['balance_after'], 2) : 'N/A';
            $redeemed  = number_format(abs((float)($tx['points_added'] ?? 0)), 2);
            $dateStr   = date('M j, Y', strtotime($tx['created_at']));
            $timeStr   = date('g:i A', strtotime($tx['created_at']));
            echo '<tr>'
                .'<td>'.($n++).'</td>'
                .'<td>'.$studNo.'</td>'
                .'<td>'.$fullName.'</td>'
                .'<td style="text-align: center;">'.$courseAb.'</td>'
                .'<td style="text-align: center;">'.$current.'</td>'
                .'<td style="text-align: center;"><span class="badge bg-danger">-'.$redeemed.'</span></td>'
                .'<td>'.$dateStr.'</td>'
                .'<td>'.$timeStr.'</td>'
                .'<td><a href="student/delete_transaction.php?id='.((int)$tx['id']).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this transaction? This cannot be undone.\')">Remove Transaction</a></td>'
                .'</tr>';
          }
          if ($n === 1) {
            echo '<tr><td colspan="8" class="text-center text-muted">No redeemed transactions yet</td></tr>';
          }
        } else {
          echo '<tr><td colspan="8" class="text-danger">Unable to load redeemed transactions.</td></tr>';
        }

        echo '</tbody></table></div>';
        // pagination controls
        if ($totalPages > 1) {
          echo '<nav><ul class="pagination justify-content-center">';
          $qsCourse = !empty($courseFilter) ? ('&course='.urlencode($courseFilter)) : '';
          $prevDisabled = ($pageNum <= 1) ? ' disabled' : '';
          $nextDisabled = ($pageNum >= $totalPages) ? ' disabled' : '';
          echo '<li class="page-item'.$prevDisabled.'"><a class="page-link" href="?page=history&p='.($pageNum-1).$qsCourse.'">Previous</a></li>';
          for ($i=1;$i<=$totalPages;$i++) {
            $active = ($i==$pageNum)?' active':'';
            echo '<li class="page-item'.$active.'"><a class="page-link" href="?page=history&p='.$i.$qsCourse.'">'.$i.'</a></li>';
          }
          echo '<li class="page-item'.$nextDisabled.'"><a class="page-link" href="?page=history&p='.($pageNum+1).$qsCourse.'">Next</a></li>';
          echo '</ul></nav>';
        }
        echo '</div></div></div>';
      } elseif($page == 'settings') {
        echo '<div class="card shadow-sm"><div class="card-body"><h5 class="text-success"><i class="bi bi-gear"></i> Settings</h5><p>Manage point system rules here.</p></div></div>';
      } else {
        echo '<div class="card shadow-sm"><div class="card-body"><h5 class="text-danger">Page not found</h5></div></div>';
      }
    ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Points auto-refresh JS -->
  <script src="assests/js/points-refresh.js"></script>
  <!-- Charts JS -->
  <script>
    // Provide PHP data to charts.js via globals
    window.courseLabels = <?= json_encode($courseLabels ?? []) ?>;
    window.courseData = <?= json_encode($courseData ?? []) ?>;
    window.courseColors = <?= json_encode(isset($courseColors) ? array_slice($courseColors, 0, isset($courseData) ? count($courseData) : 0) : []) ?>;
    window.totalPoints = <?= isset($totalPoints) ? (float)$totalPoints : 0 ?>;
  </script>
  <script src="assests/js/charts.js"></script>
  <!-- Student Modal JS -->
  <script src="assests/js/student-modal.js"></script>
  <script>
    // Simple and reliable hamburger toggle function
    function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
      if (!sidebar) return;
      
      if (window.innerWidth <= 768) {
        sidebar.classList.toggle('show');
      } else {
        sidebar.classList.toggle('hide');
      }
    }
    
    // Make function globally available
    window.toggleSidebar = toggleSidebar;
    
    // Function to clear form data when cancel is pressed
    function clearForm() {
        document.getElementById('createStudentForm').reset();
        // Clear any error messages
        const errorElements = document.querySelectorAll('#studentIdError, #cardError');
        errorElements.forEach(element => {
            if (element) {
                element.remove();
            }
        });
    }
    
    // Function to clear form data and close modal
    function clearFormAndClose() {
        // Simply redirect to clear form data
        window.location.href = 'student/clear_form_data.php';
    }
    
    // Points auto-refresh moved to assests/js/points-refresh.js
    
    // Charts moved to assests/js/charts.js

    // Ensure hamburger works immediately when page loads
    document.addEventListener("DOMContentLoaded", function() {
        // Simple click handler for hamburger
        document.addEventListener('click', function(e) {
            if (e.target.closest('.hamburger')) {
                e.preventDefault();
                toggleSidebar();
            }
        });
        
        // Initialize charts only on dashboard page
        <?php if($page == 'dashboard'): ?>
        initializeCharts();
        <?php endif; ?>
        
        // Kick off periodic refresh every 3 seconds ONLY on students page
        <?php if($page == 'students'): ?>
        setInterval(refreshVisiblePoints, 3000);
        <?php endif; ?>

        <?php if (isset($_SESSION['open_modal']) && $_SESSION['open_modal'] === true): ?>
        var myModal = new bootstrap.Modal(document.getElementById('createStudentModal'));
        myModal.show();
        <?php unset($_SESSION['open_modal']); endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
        alert('<?= addslashes($_SESSION['success_message']) ?>');
        <?php unset($_SESSION['success_message']); endif; ?>
        
        // Auto-hide error text after 5 seconds (increased from 3 seconds)
        const cardError = document.getElementById('cardError');
        const studentIdError = document.getElementById('studentIdError');
        
        if (cardError) {
            setTimeout(() => {
                cardError.style.transition = "opacity 0.5s ease";
                cardError.style.opacity = "0";
                setTimeout(() => cardError.remove(), 500);
            }, 3000);
        }
        
        if (studentIdError) {
            setTimeout(() => {
                studentIdError.style.transition = "opacity 0.5s ease";
                studentIdError.style.opacity = "0";
                setTimeout(() => studentIdError.remove(), 500);
            }, 3000);
        }
    });
  </script>
</body>
</html>
