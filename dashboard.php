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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      background-color: #f5fdf5;
      font-family: "Poppins", sans-serif;
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
    .student-table th:nth-child(1), .student-table td:nth-child(1) { width: 5%; }   /* No. */
    .student-table th:nth-child(2), .student-table td:nth-child(2) { width: 16%; }  /* Student ID */
    .student-table th:nth-child(3), .student-table td:nth-child(3) { width: 20%; }  /* Name */
    .student-table th:nth-child(4), .student-table td:nth-child(4) { width: 12%; }  /* Course */
    .student-table th:nth-child(5), .student-table td:nth-child(5) { width: 10%; }  /* Year */
    .student-table th:nth-child(6), .student-table td:nth-child(6) { width: 12%; }  /* Card No */
    .student-table th:nth-child(7), .student-table td:nth-child(7) { width: 1%; }   /* Points */
    .student-table th:nth-child(8), .student-table td:nth-child(8) { width: 15%; }  /* Actions */

    @media(max-width: 768px){
      .sidebar {transform: translateX(-100%);}
      .sidebar.show {transform: translateX(0);}
      .content {margin-left: 0;}
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <span class="hamburger" onclick="toggleSidebar()"><i class="bi bi-list"></i></span>

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
        echo '
        <div class="container-fluid">
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center">
                  <i class="bi bi-people fs-1"></i>
                  <h2 class="fw-bold">120</h2>
                  <p class="mb-0">Total Students</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center">
                  <i class="bi bi-star fs-1"></i>
                  <h2 class="fw-bold">8,450</h2>
                  <p class="mb-0">Total Points</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center">
                  <i class="bi bi-receipt fs-1"></i>
                  <h2 class="fw-bold">350</h2>
                  <p class="mb-0">Total Transactions</p>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-success"><i class="bi bi-bar-chart-line"></i> Reports & Statistics</h5>
              <p>Overview of recent activities and performance.</p>
              <div class="p-5 bg-light text-success text-center rounded">
                📊 Chart Placeholder
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
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                while($row = $result->fetch_assoc()): 
                  $middleInitial = !empty($row['middle_name']) ? strtoupper(substr($row['middle_name'], 0, 1)) . "." : "";
                ?>
                  <tr>
                    <td><?= $num++ ?></td>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['last_name'] . ", " . $row['first_name'] . " " . $middleInitial) ?></td>
                    <td><?= htmlspecialchars(courseAbbrev($row['course'])) ?></td>
                    <td><?= htmlspecialchars($row['year_level']) ?></td>
                    <td><?= htmlspecialchars($row['card_no']) ?></td>
                    <td></td>
                    <td>
                      <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editStudentModal<?= $row['id'] ?>"> Edit
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
            <div class="modal-content">
              <form action="student/create_stud.php" method="POST">
                <div class="modal-header">
                  <h5 class="modal-title">Add Student</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <label>First Name</label>
                      <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                      <label>Middle Name</label>
                      <input type="text" name="middle_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                      <label>Last Name</label>
                      <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                      <label>Student ID</label>
                      <input type="text" name="student_id" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                      <label>Course</label>
                      <input type="text" name="course" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                      <label>Year Level</label>
                      <select name="year_level" class="form-select" required>
                        <option>1st Year</option>
                        <option>2nd Year</option>
                        <option>3rd Year</option>
                        <option>4th Year</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label>Card No</label>
                      <input type="text" name="card_no" class="form-control" required>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" name="save" class="btn btn-success">Save</button>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php
      } elseif($page == 'history') {
        echo '<div class="card shadow-sm"><div class="card-body"><h5 class="text-success"><i class="bi bi-clock-history"></i> Transaction History</h5><p>Recent RFID scans and points earned.</p></div></div>';
      } elseif($page == 'settings') {
        echo '<div class="card shadow-sm"><div class="card-body"><h5 class="text-success"><i class="bi bi-gear"></i> Settings</h5><p>Manage point system rules here.</p></div></div>';
      } else {
        echo '<div class="card shadow-sm"><div class="card-body"><h5 class="text-danger">Page not found</h5></div></div>';
      }
    ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const sidebar = document.getElementById('sidebar');
    function toggleSidebar(){
      if(window.innerWidth <= 768){
        sidebar.classList.toggle('show');
      } else {
        sidebar.classList.toggle('hide');
      }
    }
  </script>
</body>
</html>
