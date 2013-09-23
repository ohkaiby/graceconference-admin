<?php
require_once 'config.php';

try {
	$dbh = new PDO(
			'mysql:host='. $config[ 'db_host' ] .
			';dbname='. $config[ 'db_name' ] .
			';port='. $config[ 'db_port' ],
		$config[ 'db_user' ],
		$config[ 'db_pass' ]
	);
	// foreach($dbh->query('SELECT * from FOO') as $row) {
	// 	print_r($row);
	// }
	// $dbh = null;
} catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}

$query = $dbh->query( 'SELECT * FROM attendees;' );
$age_map = array(
	'11' => '≤11',
	'12_17' => '12–17',
	'18' => '18+'
);
$meals_map = array(
	'meal_plan_day_1' => 'Thu',
	'meal_plan_day_2' => 'Fri',
	'meal_plan_day_3' => 'Sat',
	'meal_plan_day_4' => 'Sun',
	'meal_plan_day_5' => 'Mon'
);

// later
$children = array();
$youth = array();
$adults = array();


// use open sans
?>

<!doctype html>
<html>
<head>
	<title>Grace Conference 2013 Registration</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="grace-admin.css" rel="stylesheet" type="text/css">
</head>
<body>
	<table id="grace-table" class="table table-bordered table-condensed">
		<thead>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email</th>
			<th>Gender</th>
			<th>Age</th>
			<th>Exact Age</th>
			<th>Grade</th>
			<th>Status</th>
			<th>Undergrad Year</th>
			<th>Phone</th>
			<th>Mobile?</th>
			<th>Meals</th>
			<th>Paid</th>
		</thead>
		<tbody>
			<?php foreach( $query as $row ) : ?>
			<tr>
				<td><?php echo $row[ 'first_name' ]; ?></td>
				<td><?php echo $row[ 'last_name' ]; ?></td>
				<td><?php echo $row[ 'email' ]; ?></td>
				<td><?php echo $row[ 'gender' ]; ?></td>
				<td><?php
					echo $age_map[ $row[ 'age' ] ];
				?></td>
				<td><?php echo $row[ 'exact_age' ]; ?></td>
				<td><?php echo $row[ 'grade' ]; ?></td>
				<td><?php echo $row[ 'status' ]; ?></td>
				<td><?php echo $row[ 'undergrad_year' ]; ?></td>
				<td><?php echo $row[ 'phone' ]; ?></td>
				<td><?php echo ( $row[ 'phone_is_mobile' ] == 1 ) ? 'Yes' : 'No'; ?></td>
				<td><?php
					$meals_json = json_decode( stripcslashes( $row[ 'meal_plan' ] ), true );
					$meals_array = array();
					foreach( $meals_json as $day_key => $meals_for_day ) {
						if ( !empty( $meals_for_day[ 'breakfast' ] ) || !empty( $meals_for_day[ 'lunch' ] ) || !empty( $meals_for_day[ 'dinner' ] ) ) {
							$meals_for_day_array = array();
							$meals_for_day_str = $meals_map[ $day_key ] .': ';

							if ( !empty( $meals_for_day[ 'breakfast' ] ) ) {
								array_push( $meals_for_day_array, 'breakfast' );
							}

							if ( !empty( $meals_for_day[ 'lunch' ] ) ) {
								array_push( $meals_for_day_array, 'lunch' );
							}

							if ( !empty( $meals_for_day[ 'dinner' ] ) ) {
								array_push( $meals_for_day_array, 'dinner' );
							}

							$meals_for_day_str = $meals_for_day_str . implode( ', ', $meals_for_day_array ) .'.';
							array_push( $meals_array, $meals_for_day_str );
						}
					}

					echo implode( '<br>', $meals_array );
				?></td>
				<td><?php echo ( $row[ 'paid' ] == 1 ) ? 'Yes' : 'No'; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<script src="jquery.js"></script>
	<script src="jquery.dataTables.min.js"></script>
	<script>
	$( document ).ready( function() {
		$('#grace-table').dataTable();
	} );
	</script>
</body>
</html>