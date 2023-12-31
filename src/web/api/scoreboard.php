<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db/example_postgres_database.php';

use \IMSGlobal\LTI;
$launch = LTI\LTI_Message_Launch::from_cache($_REQUEST['launch_id'], new Example_Database());
if (!$launch->has_nrps()) {
    throw new Exception("Don't have names and roles!");
}
if (!$launch->has_ags()) {
    throw new Exception("Don't have grades!");
}
$ags = $launch->get_ags();

$score_lineitem = LTI\LTI_Lineitem::new()
    ->set_tag('score')
    ->set_score_maximum(100)
    ->set_label('Score')
    ->set_resource_id($launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
$scores = $ags->get_grades($score_lineitem);

$time_lineitem = LTI\LTI_Lineitem::new()
    ->set_tag('time')
    ->set_score_maximum(999)
    ->set_label('Time Taken')
    ->set_resource_id('time'.$launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id']);
$times = $ags->get_grades($time_lineitem);

$members = $launch->get_nrps()->get_members(true);

$scoreboard = [];

foreach ($scores as $score) {
    $result = [
        'score' => $score['resultScore'],
        'comment' => trim(strip_tags($score['comment'])),
        'user_id' => $score['userId']
    ];
    foreach ($times as $time) {
        if ($time['userId'] === $score['userId']) {
            $result['time'] = $time['resultScore'];
            break;
        }
    }
    foreach ($members as $member) {
        if ($member['user_id'] === $score['userId']) {
            $result['name'] = $member['name'];
            break;
        }
    }
    $scoreboard[] = $result;
}

$scoreboards = [];

if ($launch->has_gs()) {
    $gs = $launch->get_gs();
    $gbs = $gs->get_groups_by_set();
    $users_by_group = [];
    foreach ($members as $member) {
        foreach ($member['group_enrollments'] as $enrollment) {
            $users_by_group[$enrollment['group_id']][$member['user_id']] = $member;
        }
    }
    foreach ($gbs as $set) {
        $scoreboards[$set['id']] = [
            'name' => $set['name'],
            'id' => $set['id'],
            'scoreboard' => []
        ];
        foreach ($set['groups'] as $group_id => $group) {
            $result = [
                'score' => 0,
                'time' => 0,
                'name' => $group['name']
            ];
            foreach ($scores as $score) {
                if (isset($users_by_group[$group_id][$score['userId']])) {
                    $result['score'] += $score['resultScore'];
                }
            }
            foreach ($times as $time) {
                if (isset($users_by_group[$group_id][$time['userId']])) {
                    $result['time'] += $time['resultScore'];
                }
            }
            $scoreboards[$set['id']]['scoreboard'][] = $result;
        }
    }
}

$scoreboards["all"] = [
    'name' => 'All',
    'id' => 'all',
    'scoreboard' => $scoreboard,
];

echo json_encode($scoreboards);
?>