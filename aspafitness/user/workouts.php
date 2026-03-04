<?php
// user/workouts.php
require_once __DIR__ . '/../config/app.php';
require_login('user');

$userId = (int)$_SESSION['user_id'];
$hasAccess = user_has_workout_access($conn, $userId);

$fitnessLevels = [
    'beginner'     => 'Beginner',
    'intermediate' => 'Intermediate',
    'advanced'     => 'Advanced',
];

$fitnessGoals = [
    'weight_loss' => 'Weight Loss',
    'weight_gain' => 'Weight Gain',
    'lean_muscle' => 'Lean Muscle',
];

$error = '';
$selectedLevel = '';
$selectedGoal = '';
$generatedPlan = null;

if ($hasAccess) {
    $goalColumn = $conn->query("SHOW COLUMNS FROM workout_plans LIKE 'goal'");
    if ($goalColumn && $goalColumn->num_rows === 0) {
        $conn->query("ALTER TABLE workout_plans ADD COLUMN goal ENUM('weight_loss','weight_gain','lean_muscle') DEFAULT 'weight_loss'");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedLevel = $_POST['level'] ?? '';
        $selectedGoal  = $_POST['goal'] ?? '';

        if (!isset($fitnessLevels[$selectedLevel]) || !isset($fitnessGoals[$selectedGoal])) {
            $error = 'Please choose a valid fitness level and fitness goal.';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selectedLevel !== '' && $selectedGoal !== '' && $error === '') {
        $generatedPlanTemplates = [
            'weight_loss' => [
                'beginner' => [
                    'title' => 'Starter Fat-Loss Routine',
                    'description' => "Day 1 - Cardio + Lower Body\n- 5-10 min warm-up (treadmill slow walk)\n- Treadmill brisk walk - 15 minutes\n- Leg Press Machine - 3 x 12 reps\n- Seated Leg Curl Machine - 3 x 12 reps\n- Standing Calf Raise Machine - 3 x 15 reps\n- 5 min stretching\n\nDay 2 - Upper Body\n- 5 min warm-up (arm cycling machine if available)\n- Chest Press Machine - 3 x 12 reps\n- Lat Pulldown Machine - 3 x 12 reps\n- Dumbbell Shoulder Press (light weight) - 3 x 12 reps\n- Cable Tricep Pushdown - 3 x 12 reps\n- Light stretching\n\nDay 3 - Cardio Focus\n- 5 min warm-up\n- Cross Trainer / Elliptical - 20 minutes\n- Stationary Cycling - 10 minutes\n- Ab Crunch Machine - 3 x 15 reps\n- Plank - 3 x 20 seconds\n\nDay 4 - Full Body Circuit\n- 5 min treadmill warm-up\n- Smith Machine Squats (light weight) - 3 x 10 reps\n- Seated Row Machine - 3 x 12 reps\n- Dumbbell Lunges - 3 x 10 reps per leg\n- Cable Core Twists - 3 x 12 reps\n- Cooldown stretching\n\nDay 5 - Light Cardio + Core\n- Cycling - 15 minutes\n- Mountain Climbers - 3 x 20 seconds\n- Ab Crunch Machine - 3 x 15 reps\n- Plank - 3 x 25 seconds\n- Stretching\n\nDay 6 & 7 - Rest\n- Optional light walking\n- Proper hydration",
                ],
                'intermediate' => [
                    'title' => 'Metabolic Burn Routine',
                    'description' => "Day 1 - Lower Body + Cardio\n- 5-10 min warm-up (treadmill jog)\n- Barbell Squats - 4 x 10 reps\n- Leg Press Machine - 3 x 12 reps\n- Walking Dumbbell Lunges - 3 x 12 reps per leg\n- Leg Curl Machine - 3 x 12 reps\n- 10 min incline treadmill walk\n\nDay 2 - Upper Body Push\n- 5 min warm-up\n- Bench Press - 4 x 8-10 reps\n- Incline Dumbbell Press - 3 x 10 reps\n- Shoulder Press Machine - 3 x 12 reps\n- Cable Tricep Pushdown - 3 x 12 reps\n- 10 min cycling\n\nDay 3 - Cardio + Core\n- 5 min warm-up\n- HIIT on treadmill (1 min fast / 1 min slow x 10 rounds)\n- Ab Crunch Machine - 3 x 15 reps\n- Cable Woodchoppers - 3 x 12 reps\n- Plank - 3 x 40 seconds\n- Stretching\n\nDay 4 - Upper Body Pull\n- Lat Pulldown - 4 x 10 reps\n- Seated Row Machine - 3 x 12 reps\n- Dumbbell Bicep Curls - 3 x 12 reps\n- Face Pulls (Cable) - 3 x 12 reps\n- 15 min cross trainer\n\nDay 5 - Full Body Fat Burn Circuit\n- 5 min treadmill warm-up\n- Deadlifts - 3 x 8 reps\n- Kettlebell Swings - 3 x 15 reps\n- Battle Ropes - 3 x 30 seconds\n- Step-ups (Dumbbell) - 3 x 12 reps\n- 10 min cycling\n\nDay 6 - Light Cardio / Active Recovery\n- 20-25 min brisk walking\n- Core exercises\n- Full body stretching\n\nDay 7 - Rest\n- Proper hydration\n- Muscle recovery",
                ],
                'advanced' => [
                    'title' => 'Advanced Cut Program',
                    'description' => "Day 1 - Heavy Lower Body + HIIT\n- 5-10 min warm-up (light jog)\n- Barbell Squats - 4 x 8 reps\n- Romanian Deadlifts - 4 x 8 reps\n- Leg Press - 3 x 12 reps\n- Walking Dumbbell Lunges - 3 x 12 reps per leg\n- HIIT Treadmill (30 sec sprint / 30 sec walk x 10 rounds)\n\nDay 2 - Chest + Triceps\n- Bench Press - 4 x 8 reps\n- Incline Dumbbell Press - 3 x 10 reps\n- Cable Chest Fly - 3 x 12 reps\n- Dips - 3 x 12 reps\n- Rope Tricep Pushdown - 3 x 12 reps\n- 10-15 min cycling\n\nDay 3 - Back + Core\n- Deadlift - 4 x 6 reps\n- Pull-ups - 3 x 10 reps\n- Seated Cable Row - 3 x 12 reps\n- Face Pulls - 3 x 12 reps\n- Hanging Leg Raises - 3 x 15 reps\n- Plank - 3 x 60 seconds\n\nDay 4 - HIIT + Functional Training\n- 5 min warm-up\n- Battle Ropes - 3 x 30 sec\n- Kettlebell Swings - 3 x 15 reps\n- Box Jumps - 3 x 12 reps\n- Burpees - 3 x 12 reps\n- 15 min HIIT cycling\n\nDay 5 - Shoulders + Arms\n- Overhead Barbell Press - 4 x 8 reps\n- Lateral Raises - 3 x 12 reps\n- Barbell Bicep Curl - 3 x 10 reps\n- Cable Tricep Extension - 3 x 12 reps\n- 15 min incline walk\n\nDay 6 - Full Body Fat Burn Circuit\n- Deadlift - 3 x 8\n- Bench Press - 3 x 8\n- Squats - 3 x 8\n- Mountain Climbers - 3 x 30 sec\n- Rowing Machine - 15 minutes\n\nDay 7 - Rest / Recovery\n- Light stretching\n- Hydration\n- Muscle recovery",
                ],
            ],
            'weight_gain' => [
                'beginner' => [
                    'title' => 'Beginner Mass Foundation',
                    'description' => "Goal: Weight Gain (Muscle Mass)  |  Level: Beginner  |  Duration: 4-6 Weeks  |  Frequency: 4-5 Days/Week\n\nDay 1 - Chest + Triceps\n- 5 min warm-up\n- Chest Press Machine - 3 x 12 reps\n- Incline Dumbbell Press - 3 x 10 reps\n- Cable Chest Fly - 3 x 12 reps\n- Tricep Pushdown - 3 x 12 reps\n\nDay 2 - Back + Biceps\n- Lat Pulldown - 3 x 12 reps\n- Seated Row Machine - 3 x 12 reps\n- Dumbbell Bicep Curls - 3 x 12 reps\n- Hammer Curls - 3 x 12 reps\n\nDay 3 - Rest / Light Stretching\n\nDay 4 - Legs\n- Leg Press - 3 x 12 reps\n- Leg Curl - 3 x 12 reps\n- Smith Machine Squats - 3 x 10 reps\n- Standing Calf Raises - 3 x 15 reps\n\nDay 5 - Shoulders + Core\n- Shoulder Press Machine - 3 x 12 reps\n- Lateral Raises - 3 x 12 reps\n- Front Raises - 3 x 12 reps\n- Plank - 3 x 30 sec",
                ],
                'intermediate' => [
                    'title' => 'Hypertrophy Builder',
                    'description' => "Duration: 6-8 Weeks  |  Frequency: 5-6 Days\n\nDay 1 - Chest\n- Barbell Bench Press - 4 x 8-10\n- Incline Dumbbell Press - 3 x 10\n- Cable Fly - 3 x 12\n\nDay 2 - Back\n- Deadlift - 4 x 6\n- Lat Pulldown - 3 x 10\n- Seated Cable Row - 3 x 12\n\nDay 3 - Shoulders\n- Overhead Barbell Press - 4 x 8\n- Lateral Raises - 3 x 12\n- Face Pulls - 3 x 12\n\nDay 4 - Arms\n- Barbell Curls - 3 x 10\n- Hammer Curls - 3 x 12\n- Tricep Dips - 3 x 12\n- Rope Pushdown - 3 x 12\n\nDay 5 - Legs\n- Barbell Squats - 4 x 8\n- Leg Press - 3 x 12\n- Romanian Deadlift - 3 x 10\n- Calf Raises - 3 x 15",
                ],
                'advanced' => [
                    'title' => 'Advanced Bulk Split',
                    'description' => "Duration: 8-12 Weeks  |  Frequency: 6 Days\n\nDay 1 - Heavy Chest\n- Bench Press - 5 x 5\n- Incline Bench - 4 x 8\n- Weighted Dips - 3 x 10\n\nDay 2 - Heavy Back\n- Deadlift - 5 x 5\n- Pull-ups (Weighted) - 4 x 8\n- T-Bar Row - 3 x 10\n\nDay 3 - Shoulders\n- Military Press - 4 x 6\n- Dumbbell Lateral Raise - 4 x 12\n- Rear Delt Fly - 3 x 12\n\nDay 4 - Arms\n- Barbell Curl - 4 x 8\n- Skull Crushers - 4 x 8\n- Cable Superset (Biceps + Triceps) - 3 rounds\n\nDay 5 - Heavy Legs\n- Squats - 5 x 5\n- Leg Press - 4 x 10\n- Romanian Deadlift - 4 x 8\n- Standing Calf Raise - 4 x 15\n\nDay 6 - Full Body Hypertrophy\n- Bench Press - 3 x 10\n- Squats - 3 x 10\n- Deadlift - 3 x 8\n- Pull-ups - 3 x 10\n\nDay 7 - Rest",
                ],
            ],
            'lean_muscle' => [
                'beginner' => [
                    'title' => 'Lean Tone Starter',
                    'description' => "Frequency: 3-4 Days/Week\n\n- Full-body resistance training\n- Light cardio on off days\n- Core and posture work\n- Focus on form over heavy weight",
                ],
                'intermediate' => [
                    'title' => 'Lean Sculpt Program',
                    'description' => "Frequency: 4 Days/Week\n\n- Upper/Lower split\n- Moderate reps with controlled tempo\n- 2 conditioning sessions weekly\n- Progressive overload with moderate weights",
                ],
                'advanced' => [
                    'title' => 'Performance Lean Plan',
                    'description' => "Frequency: 5 Days/Week\n\n- Strength + athletic conditioning blend\n- Density training and supersets\n- Structured recovery sessions\n- Periodized programming for peak performance",
                ],
            ],
        ];

        $generatedPlan = $generatedPlanTemplates[$selectedGoal][$selectedLevel] ?? null;
        if ($generatedPlan === null) {
            $error = 'Unable to generate a plan for this selection. Please try again.';
        }
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="workouts-page page-section">
    <div class="page-header">
        <div>
            <h2>Workout Plans</h2>
            <p class="page-header-sub">Personalised fitness programs based on your goals</p>
        </div>
    </div>

    <?php if (!$hasAccess): ?>
        <div class="alert alert-warning">
            <strong>Membership required.</strong> Workout plans are unlocked with an active membership.
            <a href="<?= BASE_URL ?>/user/view_plans.php" class="btn btn-primary btn-sm ml-2">Browse Plans</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="workout-prompt-card">
            <form method="post">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-5 mb-2">
                        <label class="form-label">Fitness Goal</label>
                        <select name="goal" class="form-control" required>
                            <option value="">Select Goal</option>
                            <?php foreach ($fitnessGoals as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $selectedGoal === $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-5 mb-2">
                        <label class="form-label">Fitness Level</label>
                        <select name="level" class="form-control" required>
                            <option value="">Select Level</option>
                            <?php foreach ($fitnessLevels as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $selectedLevel === $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary" style="width:100%;">Generate</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($selectedLevel === '' || $selectedGoal === ''): ?>
            <p class="text-muted">Select a goal and level above to generate your personalised workout plan.</p>

        <?php elseif ($generatedPlan === null && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p class="text-muted">No plan could be generated for this combination.</p>

        <?php elseif ($generatedPlan === null): ?>
            <p class="text-muted">Press <strong>Generate</strong> to view your workout plan.</p>

        <?php else: ?>
            <div class="workout-plan-output">
                <div class="workout-plan-header">
                    <div>
                        <p class="workout-plan-title"><?= htmlspecialchars($generatedPlan['title']) ?></p>
                    </div>
                    <span class="workout-plan-badge"><?= htmlspecialchars($fitnessGoals[$selectedGoal] ?? '') ?></span>
                    <span class="workout-plan-badge"><?= htmlspecialchars($fitnessLevels[$selectedLevel] ?? '') ?></span>
                </div>
                <div class="workout-plan-body">
                    <p class="workout-plan-description"><?= htmlspecialchars($generatedPlan['description']) ?></p>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
