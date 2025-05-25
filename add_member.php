<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $gender   = $_POST['gender'];
    $plan_id  = $_POST['plan'];
    $join_date = date('Y-m-d');

    $photo = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = 'uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $photo = $dest_path;
            } else {
                $message = "Error moving the uploaded file.";
            }
        } else {
            $message = "Upload failed. Allowed file types: " . implode(", ", $allowedfileExtensions);
        }
    }

    if ($message === "") {
        $stmt = $conn->prepare("INSERT INTO members (name, email, phone, gender, plan_id, photo, join_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiss", $name, $email, $phone, $gender, $plan_id, $photo, $join_date);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Member registered successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error inserting data: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
        }
        img#preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Register New Member</h2>

    <?php if ($message) echo $message; ?>

    <form action="add_member.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Name *</label>
            <input type="text" class="form-control" id="name" name="name" required />
            <div class="invalid-feedback">Please enter the name.</div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email </label>
            <input type="email" class="form-control" id="email" name="email" />
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone </label>
            <input type="text" class="form-control" id="phone" name="phone" />
        </div>

        <div class="mb-3">
            <label for="gender" class="form-label">Gender *</label>
            <select class="form-select" id="gender" name="gender" required>
                <option value="">--Select--</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <div class="invalid-feedback">Please select a gender.</div>
        </div>

        <div class="mb-3">
            <label for="plan" class="form-label">Plan *</label>
            <select class="form-select" id="plan" name="plan" required>
                <option value="">--Select Plan--</option>
                <?php
                $result = $conn->query("SELECT * FROM plans");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='".$row['id']."'>".$row['name']." - Rs. ".$row['price']."</option>";
                }
                ?>
            </select>
            <div class="invalid-feedback">Please select a plan.</div>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Photo</label>
            <input class="form-control" type="file" id="photo" name="photo" accept="image/*" onchange="previewImage(event)" />
            <img id="preview" src="#" alt="Image Preview" style="display:none;" />
        </div>

        <button type="submit" class="btn btn-primary">Add Member</button>
    </form>
</div>

<script>
// Bootstrap form validation
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')

  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})();

// Image preview function
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');
    if(input.files && input.files[0]){
        preview.src = URL.createObjectURL(input.files[0]);
        preview.style.display = 'block';
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
}
</script>

</body>
</html>
