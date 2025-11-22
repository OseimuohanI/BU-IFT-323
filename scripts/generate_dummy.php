<?php
// Simple dummy data generator for testing.
// Usage (from project root):
//   php scripts\generate_dummy.php         # generate data (append)
//   php scripts\generate_dummy.php reset   # clear generated data then generate

if (php_sapi_name() !== 'cli') {
    exit("Run from CLI: php scripts\\generate_dummy.php\n");
}

$configPath = __DIR__ . '/../config/config.php';
if (!file_exists($configPath)) {
    exit("Missing config at {$configPath}\n");
}
$config = require $configPath;
$dbCfg = $config['db'] ?? null;
if (!$dbCfg) exit("DB config not found in config.php\n");

$host = $dbCfg['host'] ?? '127.0.0.1';
$dbname = $dbCfg['dbname'] ?? 'disciplinary';
$user = $dbCfg['user'] ?? 'root';
$pass = $dbCfg['pass'] ?? '';
$charset = $dbCfg['charset'] ?? 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$doReset = in_array('reset', $argv, true);
if ($doReset) {
    echo "Resetting generated tables (keeps OffenseType & Staff)...\n";
    // Delete generated rows only: safe approach -- remove all incidents, offenses, actions and students added by this script (EnrollmentNo prefix ENRGEN)
    $pdo->exec("DELETE ro FROM ReportOffense ro JOIN IncidentReport i ON ro.IncidentID = i.IncidentID WHERE i.Description LIKE 'GEN:%'");
    $pdo->exec("DELETE da FROM DisciplinaryAction da JOIN IncidentReport i ON da.IncidentID = i.IncidentID WHERE i.Description LIKE 'GEN:%'");
    $pdo->exec("DELETE FROM IncidentReport WHERE Description LIKE 'GEN:%'");
    $pdo->exec("DELETE FROM Student WHERE EnrollmentNo LIKE 'ENRGEN-%'");
    echo "Reset complete.\n";
}

// load existing reference data
$staff = $pdo->query("SELECT StaffID FROM Staff")->fetchAll(PDO::FETCH_COLUMN) ?: [1,2,3,4];
$offTypes = $pdo->query("SELECT OffenseTypeID FROM OffenseType")->fetchAll(PDO::FETCH_COLUMN) ?: [1,2,3,4,5,6,7,8];

$locations = ['Room 101','Room 102','Room 203','Library','Cafeteria','Playground','Art Lab','Lab 1','Hallway','Gym'];
$statuses = ['Open','Under Review','Actioned','Closed'];
$namesFirst = ['Liam','Olivia','Noah','Emma','Oliver','Ava','Elijah','Sophia','James','Isabella','Lucas','Mia','Mason','Amelia','Ethan','Harper','Logan','Evelyn','Jacob','Abigail'];
$namesLast = ['Smith','Johnson','Brown','Williams','Jones','Garcia','Miller','Davis','Rodriguez','Martinez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor'];

// generation params
$numStudents = 120;      // new students to add
$maxIncPerStudent = 8;   // up to this many incidents per student (random)
$seedPrefix = 'ENRGEN-';

$insertStudent = $pdo->prepare("INSERT INTO Student (EnrollmentNo, FirstName, LastName, DOB, Gender, Email) VALUES (:en,:fn,:ln,:dob,:g,:email)");
$insertIncident = $pdo->prepare("INSERT INTO IncidentReport (ReportDate, Location, ReporterStaffID, StudentID, Description, Status) VALUES (:rd,:loc,:rep,:sid,:desc,:status)");
$insertRo = $pdo->prepare("INSERT INTO ReportOffense (IncidentID, OffenseTypeID, Notes) VALUES (:iid,:otid,:notes)");
$insertDa = $pdo->prepare("INSERT INTO DisciplinaryAction (IncidentID, ActionType, ActionDate, DurationDays, DecisionMakerID, Notes) VALUES (:iid,:atype,:adate,:dur,:dm,:notes)");

// helper date generator
function randDate($start = '2023-01-01', $end = '2025-11-18') {
    $min = strtotime($start);
    $max = strtotime($end);
    $val = rand($min, $max);
    return date('Y-m-d H:i:s', $val);
}

echo "Adding {$numStudents} students and related incidents...\n";
$createdStudents = [];
for ($i = 1; $i <= $numStudents; $i++) {
    $fn = $namesFirst[array_rand($namesFirst)];
    $ln = $namesLast[array_rand($namesLast)];
    $en = $seedPrefix . str_pad($i, 5, '0', STR_PAD_LEFT);
    $dob = date('Y-m-d', strtotime('-' . rand(15,18) . ' years -' . rand(0,365) . ' days'));
    $g = (rand(0,1) ? 'M' : 'F');
    $email = strtolower($fn . '.' . $ln . $i . '@example.test');
    $insertStudent->execute([':en'=>$en, ':fn'=>$fn, ':ln'=>$ln, ':dob'=>$dob, ':g'=>$g, ':email'=>$email]);
    $sid = (int)$pdo->lastInsertId();
    $createdStudents[] = $sid;

    // assign random number of incidents; some students will have repeats (highly likely)
    $incCount = rand(1, $maxIncPerStudent);
    for ($j=0;$j<$incCount;$j++) {
        $rd = randDate();
        $loc = $locations[array_rand($locations)];
        $rep = $staff[array_rand($staff)];
        $status = $statuses[array_rand($statuses)];
        $desc = "GEN: Auto-generated incident for testing (batch) #{$i}-{$j}";
        $insertIncident->execute([':rd'=>$rd, ':loc'=>$loc, ':rep'=>$rep, ':sid'=>$sid, ':desc'=>$desc, ':status'=>$status]);
        $iid = (int)$pdo->lastInsertId();

        // attach 1-3 offenses
        $numOff = rand(1,3);
        $used = [];
        for ($k=0;$k<$numOff;$k++) {
            $ot = $offTypes[array_rand($offTypes)];
            // avoid duplicate same offense in one incident
            if (in_array($ot, $used, true)) continue;
            $used[] = $ot;
            $notes = "GEN: offense note";
            $insertRo->execute([':iid'=>$iid, ':otid'=>$ot, ':notes'=>$notes]);
        }

        // occasionally add disciplinary action
        if (rand(1,100) <= 40) { // ~40% of incidents get an action
            $atype = ['Warning','Counseling','Suspension','Repair','Expulsion Recommendation'][array_rand([0,1,2,3,4])];
            $adate = date('Y-m-d', strtotime($rd) + rand(0, 7) * 86400);
            $dur = (strpos($atype,'Suspension') !== false) ? rand(1,10) : 0;
            $dm = $staff[array_rand($staff)];
            $notes = "GEN: action created for incident {$iid}";
            $insertDa->execute([':iid'=>$iid, ':atype'=>$atype, ':adate'=>$adate, ':dur'=>$dur, ':dm'=>$dm, ':notes'=>$notes]);

            // introduce recidivism: for some actions, add a repeat incident within 1-6 months for same student
            if (rand(1,100) <= 20) { // 20% of actions create a repeat incident
                $repeatDate = date('Y-m-d H:i:s', strtotime($adate) + rand(10,180)*86400);
                $loc2 = $locations[array_rand($locations)];
                $status2 = $statuses[array_rand($statuses)];
                $desc2 = "GEN: Repeat incident after action for student {$sid}";
                $insertIncident->execute([':rd'=>$repeatDate, ':loc'=>$loc2, ':rep'=>$rep, ':sid'=>$sid, ':desc'=>$desc2, ':status'=>$status2]);
                $iid2 = (int)$pdo->lastInsertId();
                // attach 1 offense
                $ot2 = $offTypes[array_rand($offTypes)];
                $insertRo->execute([':iid'=>$iid2, ':otid'=>$ot2, ':notes'=>'GEN: repeat offense']);
            }
        }
    }
}

echo "Done. Created:\n";
echo " Students: " . count($createdStudents) . "\n";
$s = $pdo->query("SELECT COUNT(*) FROM IncidentReport WHERE Description LIKE 'GEN:%'")->fetchColumn();
$r = $pdo->query("SELECT COUNT(*) FROM ReportOffense WHERE Notes LIKE 'GEN:%'")->fetchColumn();
$a = $pdo->query("SELECT COUNT(*) FROM DisciplinaryAction WHERE Notes LIKE 'GEN:%'")->fetchColumn();
echo " Generated incidents: {$s}\n";
echo " Generated report offenses: {$r}\n";
echo " Generated disciplinary actions: {$a}\n";
echo "Seeding complete.\n";