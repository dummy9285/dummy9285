<?php
	session_start();
	include_once("header.php");
	include_once("navbar.php");
	include_once("../includes/libs/dbh.php");

	$dbConnection = new dbConnection();
	$conn = $dbConnection->connect();
	$studentId  = $_SESSION['studentId'];


	function isPending($conn, $studentId) {
		$sql = "SELECT payment_id FROM student_payment WHERE student_id=$studentId AND status='pending'";
		$result = $conn->query($sql);
		$rowCount = $result->num_rows;

		if ($rowCount > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	function isEnrolled($conn, $studentId) {
		$isPending =  isPending($conn, $studentId);

		if ($isPending == true) {
			echo
			'<div class="col-lg-6">
				<span class="font-weight-bold">Kindly wait for the admin to book your class.</span>
			</div>';
		}
		else {
			$sql = "SELECT end_date FROM session_details WHERE student_id=$studentId AND end_date >= curdate()";
			$result = $conn->query($sql);
			$rowCount = $result->num_rows;

			if ($rowCount > 0) {
				if ($row = $result->fetch_assoc()) {
					$date = date('F d, Y', strtotime($row['end_date']));
					echo 
					'<div class="col-lg-6">
						<span class="font-weight-bold">You cannot enroll for a class now because you are currently enrolled until '.$date.'.</span>
					</div>';
				}
			}
			else {
				echo 
				'<div class="col-lg-4">
					<div class="panel panel-info">
						<div class="panel-heading">
							Paypal Payment Form
						</div>
						<div class="panel-body ">
							<form action="payment-insert.php" method="POST" onsubmit="return confirm(\'Are you sure to submit this payment?\')">							
								<div class="form-group">
									<input class="form-control" type="email" name="email" placeholder="Email address" required>
								</div>
								<div class="form-group">
									<input class="form-control" type="password" name="password" placeholder="Password" required>
								</div>
								<div class="form-group">
									<label for="">Number of Session</label>
									<div class="input-group margin-bottom-sm">
										<span class="input-group-addon"><i class="fa fa-clock-o fa-fw"></i></span>
										<input id="session" class="form-control" type="number" name="session" min="10" min="200" placeholder="minimum of 10 sessions" required>
									</div>
								</div>
								<div class="form-group">
									<label class="text-center form-control bg-primary text-white font-weight-bold">Amount: &#36; <span id="amount-text">0:00</span></label>
									<input id="amount" type="hidden" name="amount" value="">
								</div>
								<div class="form-group">
									<input class="btn btn-block btn-success" id="submit" type="submit" name="submit" value="Pay now">
								</div>
							</form>
						</div>
					</div>
				</div>';
			}
		}
	}
?>
<body class="fixed-nav sticky-footer" id="page-top">
	<div class="content-wrapper">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					Make a Payment
				</div>
				<!-- /.panel-heading -->
				<div class="card-body">
					<div class="row">
						<?php
							isEnrolled($conn, $studentId);
						?>
					</div>
          <!-- /.row-->
        </div>
				<!-- panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.container-fluid -->
		<?php include_once("footer.php") ?>
	</div>
	<!-- /.content-wrapper -->
	<?php include_once("js.php") ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#session').change(function() {
				var amount = ($(this).val() * 2.50).toFixed(2);
				$('#amount-text').text(amount);
				$('#amount').val(amount);
			});
		});
		// $(document).ready(function() {
		// 	$('#total').hide();
		// });
		// $('#session').change(function() {
		// 	var input = $(this).val();
		// 	if (input.length <= 0) {
		// 		$('#total').hide();
		// 	}
		// 	else {
		// 		var amount = input * 2.50;
		// 		$('#total').html('<label>Amount:&nbsp;&nbsp;&nbsp;&nbsp;&#36;' + amount + '</label>');
		// 		$('#total').show();
		// 	}
		// });
	</script>
</body>
