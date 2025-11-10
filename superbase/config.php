<?php
    // Database configuration
    $supabaseUrl = 'https://jmfgsgatvkzofwmbciqp.supabase.co';
    $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImptZmdzZ2F0dmt6b2Z3bWJjaXFwIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTYzOTUwNzcsImV4cCI6MjA3MTk3MTA3N30.GlSDSeO4ZUTR4DZhMCe9k7DBcgnbrP8JgvVX8CNuUAo'; 

    // Array of table names
    $tableNames = [
        'admin_login',
        'customer_details',
        'exam_category',
        'exam_results',
        'gender',
        'questions',
        'registration'
    ];

 


   


    function fetchData($tableName) {
        global $supabaseUrl, $supabaseKey;

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $tableName);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $supabaseKey,
            'apikey: ' . $supabaseKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }

        curl_close($ch);

        // Decode and return JSON response
        return json_decode($response, true);
    }
    function fetchIncrease($mainTable, ...$joinTables) {
        global $supabaseUrl, $supabaseKey;
        
        // Parse main table and columns
        $mainTableParts = parseTableWithColumns($mainTable);
        $mainTableName = $mainTableParts['table'];
        $mainTableAlias = $mainTableParts['alias'] ?? substr($mainTableName, 0, 2);
        $mainColumns = $mainTableParts['columns'] ?? ['*'];
    
        // Build SELECT clause
        $selectColumns = [];
        foreach ($mainColumns as $col) {
            $selectColumns[] = $mainTableAlias . '.' . $col . ' AS ' . $mainTableAlias . '_' . $col;
        }
        
        $joinClauses = [];
        $joinCounter = 1;
        
        // Process join tables
        foreach ($joinTables as $joinTable) {
            $joinParts = parseTableWithColumns($joinTable);
            $joinTableName = $joinParts['table'];
            $joinTableAlias = $joinParts['alias'] ?? substr($joinTableName, 0, 2) . $joinCounter;
            $joinColumns = $joinParts['columns'] ?? ['*'];
            $joinOnColumn = $joinParts['on_column'] ?? 'id';
            
            // Add join columns to SELECT
            foreach ($joinColumns as $col) {
                $selectColumns[] = $joinTableAlias . '.' . $col . ' AS ' . $joinTableAlias . '_' . $col;
            }
            
            // Build join clause
            $previousAlias = ($joinCounter === 1) ? $mainTableAlias : substr($joinTableName, 0, 2) . ($joinCounter - 1);
            $joinClauses[] = "LEFT JOIN $joinTableName AS $joinTableAlias ON $previousAlias.$joinOnColumn = $joinTableAlias.$joinOnColumn";
            
            $joinCounter++;
        }
        
        // Build final SQL query
        $selectClause = implode(', ', $selectColumns);
        $fromClause = "$mainTableName AS $mainTableAlias";
        $joinClause = implode(' ', $joinClauses);
    
        $query = "SELECT $selectClause FROM $fromClause $joinClause";
        
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $mainTableName . '?select=' . urlencode($query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $supabaseKey,
            'apikey: ' . $supabaseKey,
            'Content-Type: application/json',
            'Prefer: params=multiple-objects'
        ]);
        
        $response = curl_exec($ch);
        
        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }
        
        curl_close($ch);
        
        // Decode and return JSON response
        return json_decode($response, true);
    }

    function fetchIncreaseWithWhere($mainTable, $whereClause, ...$joinTables) {
        global $supabaseUrl, $supabaseKey;
        $mainTableParts = parseTableWithColumns($mainTable);
        $mainTableName = $mainTableParts['table'];
        $mainColumns = $mainTableParts['columns'] ?? ['*'];
        
        // Build SELECT clause without aliases
        $selectColumns = [];
        foreach ($mainColumns as $col) {
            $selectColumns[] = $mainTableName . '.' . $col;
        }
        
        $joinClauses = [];
        
        // Process join tables
        foreach ($joinTables as $joinTable) {
            $joinParts = parseTableWithColumns($joinTable);
            $joinTableName = $joinParts['table'];
            $joinColumns = $joinParts['columns'] ?? ['*'];
            $joinOnColumn = $joinParts['on_column'] ?? 'id';
            
            // Add join columns to SELECT without aliases
            foreach ($joinColumns as $col) {
                $selectColumns[] = $joinTableName . '.' . $col;
            }
            
            // Build join clause using actual table names
            $joinCondition = $mainTableName . '.' . $joinOnColumn . ' = ' . $joinTableName . '.' . $joinOnColumn;
            $joinClauses[] = "LEFT JOIN $joinTableName ON $joinCondition";
        }
        
        // Build final SQL query
        $selectClause = implode(', ', $selectColumns);
        $fromClause = $mainTableName;
        $joinClause = implode(' ', $joinClauses);
        
        $query = "SELECT $selectClause FROM $fromClause $joinClause WHERE $whereClause";
        
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $mainTableName . '?select=' . urlencode($query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $supabaseKey,
            'apikey: ' . $supabaseKey,
            'Content-Type: ' . 'application/json',
            'Prefer: params=multiple-objects'
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }
        
        curl_close($ch);
        
        return json_decode($response, true);
    }

    // Helper function to parse table with columns (simplified)
    function parseTableWithColumns($tableString) {
        $result = [];
        
        // Check if table has columns specified in parentheses
        if (preg_match('/([a-zA-Z_]+)(?:\(([^\)]+)\))?/', $tableString, $matches)) {
            $result['table'] = $matches[1];
            
            if (isset($matches[2])) {
                $parts = explode(',', $matches[2]);
                $columns = [];
                
                foreach ($parts as $part) {
                    $part = trim($part);
                    // Check for join condition (table:column format)
                    if (strpos($part, ':') !== false) {
                        list($tablePart, $onColumn) = explode(':', $part);
                        $result['on_column'] = trim($onColumn);
                    } else {
                        $columns[] = $part;
                    }
                }
                
                if (!empty($columns)) {
                    $result['columns'] = $columns;
                }
            }
        }
        
        return $result;
    }
    
    function createData($tableName, $data) {
        global $supabaseUrl, $supabaseKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $tableName);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $supabaseKey,
            'apikey: ' . $supabaseKey,
            'Content-Type: application/json',
            'Prefer: return=representation' //return the inserted data
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        
        // Check if cURL encountered an error
        if (curl_errno($ch)) {
            return ['error' => curl_error($ch)];
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the response
        $decodedResponse = json_decode($response, true);

        // Check for errors in the response
        if (isset($decodedResponse['code']) && isset($decodedResponse['message'])) {
            return [
                'error' => 'Supabase error: ' . htmlspecialchars($decodedResponse['message'])
            ];
        }

        // Return the decoded response (should be the inserted data)
        return $decodedResponse;
    }


    function updateData($tableName, $id, $data) {
        global $supabaseUrl, $supabaseKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $tableName . '?id=eq.' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $supabaseKey,
            'apikey: ' . $supabaseKey,
            'Content-Type: application/json',
            'Prefer: return=representation' // To return the updated record
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return ['error' => curl_error($ch)];
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    
    function deleteData($tableName, $id) {
        global $supabaseUrl, $supabaseKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $tableName . '?id=eq.' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $supabaseKey,
            'apikey: ' . $supabaseKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return ['error' => curl_error($ch)];
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    function deleteDataImproved($tableName, $column, $value) {
        global $supabaseUrl, $supabaseKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $tableName . '?' . $column . '=eq.' . $value);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $supabaseKey,
            'apikey: ' . $supabaseKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return ['error' => curl_error($ch)];
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    function extractSupabaseErrorMessage($error) {
        // If it's already a string error message
        if (is_string($error)) {
            // Check for common Supabase error patterns
            if (strpos($error, 'duplicate key') !== false) {
                return "This exam category already exists. Please use a different name.";
            }
            if (strpos($error, 'violates unique constraint') !== false) {
                return "This exam category name is already taken. Please choose a different name.";
            }
            if (strpos($error, 'violates not-null constraint') !== false) {
                return "Please fill in all required fields.";
            }
            if (strpos($error, 'network') !== false || strpos($error, 'curl') !== false) {
                return "Network error. Please check your connection and try again.";
            }
            return $error; // Return the original error if no pattern matches
        }
        
        // If it's an array (Supabase error response)
        if (is_array($error)) {
            // Check for Supabase API error format
            if (isset($error['message'])) {
                $message = $error['message'];
                
                // Handle specific Supabase error codes
                if (isset($error['code'])) {
                    switch ($error['code']) {
                        case '23505': // Unique violation
                            return "This exam category already exists. Please use a different name.";
                        case '23502': // Not null violation
                            return "Please fill in all required fields.";
                        case '23503': // Foreign key violation
                            return "Cannot update exam category due to related records.";
                        case '22P02': // Invalid input syntax
                            return "Invalid input format. Please check your data.";
                        default:
                            return $message . " (Error code: " . $error['code'] . ")";
                    }
                }
                return $message;
            }
        
            // Check for details array in Supabase error
            if (isset($error['details'])) {
                return $error['details'];
            }
            
            // Check for error array in Supabase response
            if (isset($error['error'])) {
                return $error['error'];
            }
        }
            
            // Default fallback
            return "An unexpected error occurred. Please try again.";
    }

    function updateUserStatus($tableName, $unique_id, $data) {
        global $supabaseUrl, $supabaseKey;

        $ch = curl_init();
        // Use unique_id instead of id for the column name
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $tableName . '?unique_id=eq.' . $unique_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $supabaseKey,
            'apikey: ' . $supabaseKey,
            'Content-Type: application/json',
            'Prefer: return=representation' // To return the updated record
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => 'CURL Error: ' . $error];
        }

        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);
        
        // Check for HTTP errors
        if ($httpCode >= 400) {
            return ['error' => "HTTP $httpCode: " . ($decodedResponse['message'] ?? 'Update failed')];
        }
    
        return $decodedResponse;
    }

// ==================================================
// NEW DYNAMIC FUNCTIONS - SAFE TO ADD
// These won't interfere with existing functions
// ==================================================

/**
 * Dynamic join function that works with any tables
 * @param string $mainTable - Main table name
 * @param array $joinConfig - Join configuration [['table' => 'table2', 'on' => 'column']]
 * @param array $conditions - Where conditions ['column' => 'value']
 * @param array $select - Columns to select (empty = all)
 * @return array
 */
function dynamicFetchWithJoin($mainTable, $joinConfig = [], $conditions = [], $select = []) {
    global $supabaseUrl, $supabaseKey;
    
    // Build select clause
    $selectClause = '*';
    if (!empty($select)) {
        $selectClause = implode(',', $select);
    }
    
    // Build query
    $query = "{$mainTable}?select={$selectClause}";
    
    // Add joins using Supabase's nested syntax
    foreach ($joinConfig as $join) {
        $joinTable = $join['table'];
        $joinOn = $join['on'] ?? 'id';
        $query .= ",{$joinTable}(*)";
    }
    
    // Add conditions
    foreach ($conditions as $column => $value) {
        $operator = 'eq';
        $query .= "&{$column}={$operator}.{$value}";
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $supabaseKey,
        'apikey: ' . $supabaseKey,
        'Content-Type: ' . 'application/json'
    ]);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ['error' => $error_msg];
    }
    
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Universal data fetcher with flexible filtering
 * @param string $table - Table name
 * @param array $filters - Optional filters ['column' => 'value']
 * @param array $select - Optional columns to select
 * @param string $orderBy - Optional order by column
 * @param int $limit - Optional limit
 * @return array
 */
function universalFetch($table, $filters = [], $select = [], $orderBy = '', $limit = null) {
    global $supabaseUrl, $supabaseKey;
    
    // Build select clause
    $selectClause = empty($select) ? '*' : implode(',', $select);
    $query = "{$table}?select={$selectClause}";
    
    // Add filters
    foreach ($filters as $column => $value) {
        $query .= "&{$column}=eq.{$value}";
    }
    
    // Add ordering
    if (!empty($orderBy)) {
        $query .= "&order={$orderBy}";
    }
    
    // Add limit
    if ($limit !== null) {
        $query .= "&limit={$limit}";
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/' . $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $supabaseKey,
        'apikey: ' . $supabaseKey,
        'Content-Type: ' . 'application/json'
    ]);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ['error' => $error_msg];
    }
    
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Safe exam results fetcher with category data
 * Uses the universal functions without affecting existing code
 */
function safeFetchExamResultsWithCategory($unique_id) {
    // Method 1: Try using dynamic join
    $result = dynamicFetchWithJoin(
        'exam_results',
        [['table' => 'exam_category', 'on' => 'exam_type']],
        ['unique_id' => $unique_id]
    );
    
    // If join worked and we have category data, return it
    if (!empty($result) && !isset($result['error']) && isset($result[0]['exam_category'])) {
        return $result;
    }
    
    // Method 2: Fallback to separate queries
    $examResults = universalFetch('exam_results', ['unique_id' => $unique_id]);
    
    if (empty($examResults) || isset($examResults['error'])) {
        return $examResults;
    }
    
    // Get all categories
    $categories = universalFetch('exam_category');
    $categoryMap = [];
    
    if (is_array($categories) && !isset($categories['error'])) {
        foreach ($categories as $category) {
            $categoryMap[$category['category']] = $category;
        }
    }
    
    // Combine data
    $combinedResults = [];
    foreach ($examResults as $result) {
        $examType = $result['exam_type'];
        $categoryInfo = $categoryMap[$examType] ?? [];
        
        $combinedResults[] = array_merge($result, [
            'exam_category' => $categoryInfo
        ]);
    }
    
    return $combinedResults;
}
?>