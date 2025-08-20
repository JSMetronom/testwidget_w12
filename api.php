<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$eventsFile = 'events.json';

function loadEvents() {
    global $eventsFile;
    if (file_exists($eventsFile)) {
        $content = file_get_contents($eventsFile);
        return json_decode($content, true) ?: [];
    }
    return [];
}

function saveEvents($events) {
    global $eventsFile;
    $json = json_encode($events, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($eventsFile, $json) !== false;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        if ($action === 'get') {
            $events = loadEvents();
            echo json_encode([
                'success' => true,
                'events' => $events
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid action'
            ]);
        }
    } 
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        if ($action === 'save') {
            $events = $input['events'] ?? [];
            
            if (saveEvents($events)) {
                echo json_encode([
                    'success' => true
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to save events'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid action'
            ]);
        }
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
