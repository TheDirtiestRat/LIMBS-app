<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1,
            shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Login </title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="../css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <div class="d-flex" id="wrapper">

            <!-- Page content wrapper-->
            <div id="page-content-wrapper">

                <!-- Page content-->
                <div class="container-fluid">
                    <form class=" m-3 row align-items-lg-center
                        justify-content-lg-center" style="height: 90vh;"
                        action="login.php" method="post">
                        <div class="col-lg-6">
                            <p class="lead m-0">School Inventory Monitoring with
                                Barcode System</p>
                            <h1 class=" display-1">Log-in</h1>
                            <hr>

                            <div class="row mb-3">
                                <label for="inputEmail3" class="col-sm-2
                                    col-form-label">User Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control"
                                        name="userName" id="inputUname3">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputPassword3" class="col-sm-2
                                    col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control"
                                        name="passWord" id="inputPassword3">
                                </div>
                            </div>

                            <!-- The alert login -->
                            <?php 
                            if (isset($_GET['error'])) {
                                echo '<div class="alert alert-danger alert-dismissible
                                fade show" role="alert">
                                '. $_GET['error'] .'
                                <button type="button" class="btn-close"
                                    data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                            }else {
                                echo '';
                            }
                            ?>
                            

                            <hr>
                            <button type="submit" class="btn btn-dark">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bootstrap core JS-->
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="../js/scripts.js"></script>
    </body>
</html>
