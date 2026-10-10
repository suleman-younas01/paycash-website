<?php
require("config/env.php");
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: *');

if ($_GET['page_name'] == "view_allFaqs") {
    global $h;
    $srNo = 0;
    $viewFaqs = $h->table('faqs')->select()->orderBy('id', 'desc')->fetchAll();
    if (!empty($viewFaqs)) {
        foreach ($viewFaqs as $viewFaq) {
            $action = array('action' => '
                <button type="button" class="btn btn-success edit btn-sm" data-bs-toggle="modal" data-id=" '. $viewFaq["id"] .' " data-bs-target="#editExampleModal" ><i style="font-size: 16px;" class="fa-regular fa-edit"></i></button>
                <a href="javascript:;" class="btn-sm btn btn-light-danger text-start me-2 action-edit" onclick="deleteUser(' . $viewFaq["id"] . ')" ><i style="font-size: 16px;" class="fa-regular fa-trash-can"></i></a>
            ');
            $Question= $viewFaq['question'];
            $Answer= ($viewFaq['answer']);

            $srNo++;
            $ids = array("ids" => "$srNo");
            $Questions = array("question" => "$Question");
            $Answers = array("answer" => "$Answer");

            $check_arr[] = array_merge($ids, $viewFaq, $Questions, $Answers,$action);
        }

        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => count($check_arr),
            "iTotalDisplayRecords" => count($check_arr),
            "aaData" => $check_arr
        );
        echo json_encode($result);
    } else {
        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => array()
        );
        echo json_encode($result);
    }
}

if ($_GET['page_name'] == "view_allTransactions") {
    $srNo = 0;
    $transactions = $h->table('transactions')->select()->orderBy('id', 'desc')->fetchAll();

    if (!empty($transactions)) {
        foreach ($transactions as $transaction) {
            // Fetch sender & receiver names separately
            $senderName = "N/A";
            $receiverName = "-";

            if ($transaction['transaction_type'] == "deposit" || $transaction['transaction_type'] == "withdraw") {
                $sender = $h->table('users')->select()->where('id', '=', $transaction['user_id'])->fetchAll();
                if ($sender[0]["profile_img"] != '' && $sender[0]["profile_img"] != 'null'){
                    $profile_image = $sender[0]["profile_img"];
                }else{
                    $profile_image = "avatar.png";
                }
                $senderName = '<div class="d-flex align-items-center"> <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
															<a href="#">
																<div class="symbol-label">
																	<img src="'.$env["APP_URL"].'uploads/profile/'.$profile_image.'" alt="Emma Smith" class="w-100">
																</div>
															</a>
														</div> <div class="d-flex flex-column">
															<a href="#" class="text-gray-800 text-hover-primary mb-1">' . $sender[0]['fname'] .' '.  $sender[0]['lname'] . '</a>
															<span>'.  $sender[0]['email'] . '</span>
														</div></div>';
            } elseif ($transaction['transaction_type'] == "transfer") {
                if (!empty($transaction['sender_id'])) {
                    $sender = $h->table('users')->select()->where('id', '=', $transaction['sender_id'])->fetchAll();
                    if ($sender[0]["profile_img"] != '' && $sender[0]["profile_img"] != 'null'){
                        $profile_image = $sender[0]["profile_img"];
                    }else{
                        $profile_image = "avatar.png";
                    }
                    $senderName ='<div class="d-flex align-items-center"> <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
															<a href="#">
																<div class="symbol-label">
																	<img src="'.$env["APP_URL"].'uploads/profile/'.$profile_image.'" alt="Emma Smith" class="w-100">
																</div>
															</a>
														</div> <div class="d-flex flex-column">
															<a href="#" class="text-gray-800 text-hover-primary mb-1">' . $sender[0]['fname'] .' '.  $sender[0]['lname'] . '</a>
															<span>'.  $sender[0]['email'] . '</span>
														</div></div>';
                }
                if (!empty($transaction['recipient_id'])) {
                    $recipient = $h->table('users')->select()->where('id', '=', $transaction['recipient_id'])->fetchAll();
                    if ($recipient[0]["profile_img"] != '' && $recipient[0]["profile_img"] != 'null'){
                        $profile_image = $recipient[0]["profile_img"];
                    }else{
                        $profile_image = "avatar.png";
                    }
                    $receiverName ='<div class="d-flex align-items-center"> <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
															<a href="#">
																<div class="symbol-label">
																	<img src="'.$env["APP_URL"].'uploads/profile/'.$profile_image.'" alt="Emma Smith" class="w-100">
																</div>
															</a>
														</div> <div class="d-flex flex-column">
															<a href="#" class="text-gray-800 text-hover-primary mb-1">' . $recipient[0]['fname'] .' '.  $recipient[0]['lname'] . '</a>
															<span>'.  $recipient[0]['email'] . '</span>
														</div></div>';
                }
            }

            // Type Badge
            $typeBadge = "<span class='badge ";
            if ($transaction['transaction_type'] == "deposit") {
                $typeBadge .= "badge-light-success'>Deposit</span>";
            } elseif ($transaction['transaction_type'] == "withdraw") {
                $typeBadge .= "badge-light-danger'>Withdraw</span>";
            } else {
                $typeBadge .= "badge-light-primary'>Transfer</span>";
            }

            // Display Amount
            if ($transaction['transaction_type'] == "transfer") {
                $amountDisplay = "USD " . number_format($transaction['amount_in_usd'], 2);
            } else {
                $amountDisplay = strtoupper($transaction['currency']) . " " . number_format($transaction['amount'], 2);
            }

            // Status Badge
            $statusView = ($transaction['status'] == "completed") ?
                "<span class='badge badge-light-success'>Completed</span>" :
                "<span class='badge badge-light-warning'>Pending</span>";

            // Action Button
            $action = '<a href="#" data-id="' . $transaction["id"] . '" class="viewTransaction btn btn-primary btn-sm">View</a>';
            // Date & Time Formatting
            $dateTime = date("d M Y H:i", strtotime($transaction['created_at']));

            $srNo++;
            $check_arr[] = array_merge(
                ["ids" => $srNo],
                ["transaction_type" => $typeBadge],
                ["sender_name" => $senderName],
                ["system_transactionID" => $transaction['system_transaction_id']],
                ["receiver_name" => ($transaction['transaction_type'] == 'transfer') ? $receiverName : "-"],
                ["amount" => $amountDisplay],
                ["amount_in_usd" => "USD " . number_format($transaction['amount_in_usd'], 2)],
                ["date_time" => $dateTime],  // NEW DATE & TIME COLUMN
                ["status" => $statusView],
                ["action" => $action]
            );
        }

        $result = [
            "sEcho" => 1,
            "iTotalRecords" => count($check_arr),
            "iTotalDisplayRecords" => count($check_arr),
            "aaData" => $check_arr
        ];
        echo json_encode($result);
    } else {
        echo json_encode(["sEcho" => 1, "iTotalRecords" => 0, "iTotalDisplayRecords" => 0, "aaData" => []]);
    }
}


if($_GET['page_name']=="view_allUsers"){
    $srNo = 0;
    $users = $h->table('users')->select()->where('type','=','user')->orderBy('id', 'desc')->fetchAll();

    if (!empty($users)) {
        foreach ($users as $user) {
           $balance= getUserBalance($user['id']);
            $balanceView= array("balanceView" => "USD ".number_format($balance, 2));
            // Determine user status
            if ($user['status'] == "active") {
                $statusView = "<span class='badge badge-light-success'>Active</span>";
                $userStatus = "<a href='javascript:;' onclick='userStatus(".$user['id'].", 0)' class='btn btn-light-success btn-sm text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-unlock'></i></a>";
            } else if ($user['status'] == "block") {
                $statusView = "<span class='badge badge-light-danger'>Inactive</span>";
                $userStatus = "<a href='javascript:;' onclick='userStatus(".$user['id'].", 1)' class='btn-sm btn btn-light-danger text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-lock'></i></a>";
            } else {
                $statusView = "<span class='badge badge-light-danger'>Inactive</span>";
                $userStatus = "<a href='javascript:;' onclick='userStatus(".$user['id'].", 1)' class='btn-sm btn btn-light-danger text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-lock'></i></a>";
            }

            // $plus = array("plusView" => "<a class='control' tabindex='0' style=""></a>");
            $action = array('action' =>  $userStatus.'
                   <a href="javascript:;" class="btn-sm btn btn-light-danger text-start me-2 action-edit" onclick="deleteUser('.$user["id"].')" ><i style="font-size: 16px;" class="fa-regular fa-trash-can"></i></a>
           
                      ');

            $status = array("statusView" => $statusView);

            if ($user["profile_img"] != '' && $user["profile_img"] != 'null'){
                $profile_image = $user["profile_img"];
            }else{
                $profile_image = "avatar.png";
            }
            $userView = array(
                "userView" => '<div class="d-flex align-items-center"> <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
															<a href="#">
																<div class="symbol-label">
																	<img src="'.$env["APP_URL"].'uploads/profile/'.$profile_image.'" alt="Emma Smith" class="w-100">
																</div>
															</a>
														</div> <div class="d-flex flex-column">
															<a href="#" class="text-gray-800 text-hover-primary mb-1">' . $user['fname'] .' '.  $user['lname'] . '</a>
															<span>'.  $user['email'] . '</span>
														</div></div>'
            );

            $srNo++;
            $ids = array("ids" => "$srNo");
            $check_arr[] = array_merge($ids,$balanceView, $user,$userView, $status, $action);
        }

        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => count($check_arr),
            "iTotalDisplayRecords" => count($check_arr),
            "aaData" => $check_arr
        );
        echo json_encode($result);
    } else {
        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => array()
        );
        echo json_encode($result);
    }

}
if($_GET['page_name']=="view_activeUsers"){
    $srNo = 0;
    $activeUsers = $h->table('users')->select()->where('type','=','user')->where('status','=','active')->orderBy('id', 'desc')->fetchAll();

    if (!empty($activeUsers)) {
        foreach ($activeUsers as $user) {
            $balance= getUserBalance($user['id']);
            $balanceView= array("balanceView" => "USD ".number_format($balance, 2));
            // Determine user status
            if ($user['status'] == "active") {
                $statusView = "<span class='badge badge-light-success'>Active</span>";
                $userStatus = "<a href='javascript:;' onclick='userStatus(".$user['id'].", 0)' class='btn btn-light-success btn-sm text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-unlock'></i></a>";
            } else if ($user['status'] == "block") {
                $statusView = "<span class='badge badge-light-danger'>Inactive</span>";
                $userStatus = "<a href='javascript:;' onclick='userStatus(".$user['id'].", 1)' class='btn-sm btn btn-light-danger text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-lock'></i></a>";
            } else {
                $statusView = "<span class='badge badge-light-danger'>Inactive</span>";
                $userStatus = "<a href='javascript:;' onclick='userStatus(".$user['id'].", 1)' class='btn-sm btn btn-light-danger text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-lock'></i></a>";
            }

            // $plus = array("plusView" => "<a class='control' tabindex='0' style=""></a>");
            $action = array('action' =>  $userStatus.'
                   <a href="javascript:;" class="btn-sm btn btn-light-danger text-start me-2 action-edit" onclick="deleteUser('.$user["id"].')" ><i style="font-size: 16px;" class="fa-regular fa-trash-can"></i></a>
           
                      ');

            $status = array("statusView" => $statusView);

            if ($user["profile_img"] != '' && $user["profile_img"] != 'null'){
                $profile_image = $user["profile_img"];
            }else{
                $profile_image = "avatar.png";
            }
            $userView = array(
                "userView" => '<div class="d-flex align-items-center"> <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
															<a href="#">
																<div class="symbol-label">
																	<img src="'.$env["APP_URL"].'uploads/profile/'.$profile_image.'" alt="Emma Smith" class="w-100">
																</div>
															</a>
														</div> <div class="d-flex flex-column">
															<a href="#" class="text-gray-800 text-hover-primary mb-1">' . $user['fname'] .' '.  $user['lname'] . '</a>
															<span>'.  $user['email'] . '</span>
														</div></div>'
            );

            $srNo++;
            $ids = array("ids" => "$srNo");
            $check_arr[] = array_merge($ids,$balanceView, $user,$userView, $status, $action);
        }

        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => count($check_arr),
            "iTotalDisplayRecords" => count($check_arr),
            "aaData" => $check_arr
        );
        echo json_encode($result);
    } else {
        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => array()
        );
        echo json_encode($result);
    }

}
if($_GET['page_name']=="view_blockUsers"){
    $srNo = 0;
    $activeUsers = $h->table('users')->select()->where('type','=','user')->where('status','=','block')->orderBy('id', 'desc')->fetchAll();

    if (!empty($activeUsers)) {
        foreach ($activeUsers as $user) {
            $balance= getUserBalance($user['id']);
            $balanceView= array("balanceView" => "USD ".number_format($balance, 2));
            // Determine user status
            if ($user['status'] == "active") {
                $statusView = "<span class='badge badge-light-success'>Active</span>";
                $userStatus = "<a href='javascript:;' onclick='userStatus(".$user['id'].", 0)' class='btn btn-light-success btn-sm text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-unlock'></i></a>";
            } else if ($user['status'] == "block") {
                $statusView = "<span class='badge badge-light-danger'>Inactive</span>";
                $userStatus = "<a href='javascript:;' onclick='userStatus(".$user['id'].", 1)' class='btn-sm btn btn-light-danger text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-lock'></i></a>";
            } else {
                $statusView = "<span class='badge badge-light-danger'>Inactive</span>";
                $userStatus = "<a href='javascript:;' onclick='userStatus(".$user['id'].", 1)' class='btn-sm btn btn-light-danger text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-lock'></i></a>";
            }

            // $plus = array("plusView" => "<a class='control' tabindex='0' style=""></a>");
            $action = array('action' =>  $userStatus.'
                   <a href="javascript:;" class="btn-sm btn btn-light-danger text-start me-2 action-edit" onclick="deleteUser('.$user["id"].')" ><i style="font-size: 16px;" class="fa-regular fa-trash-can"></i></a>
           
                      ');

            $status = array("statusView" => $statusView);

            if ($user["profile_img"] != '' && $user["profile_img"] != 'null'){
                $profile_image = $user["profile_img"];
            }else{
                $profile_image = "avatar.png";
            }
            $userView = array(
                "userView" => '<div class="d-flex align-items-center"> <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
															<a href="#">
																<div class="symbol-label">
																	<img src="'.$env["APP_URL"].'uploads/profile/'.$profile_image.'" alt="Emma Smith" class="w-100">
																</div>
															</a>
														</div> <div class="d-flex flex-column">
															<a href="#" class="text-gray-800 text-hover-primary mb-1">' . $user['fname'] .' '.  $user['lname'] . '</a>
															<span>'.  $user['email'] . '</span>
														</div></div>'
            );

            $srNo++;
            $ids = array("ids" => "$srNo");
            $check_arr[] = array_merge($ids,$balanceView, $user,$userView, $status, $action);
        }

        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => count($check_arr),
            "iTotalDisplayRecords" => count($check_arr),
            "aaData" => $check_arr
        );
        echo json_encode($result);
    } else {
        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => array()
        );
        echo json_encode($result);
    }

}

if ($_GET['page_name'] == "view_cryptoCurrencies") {
    global $h;
    $srNo = 0;

    // Make sure $h is correctly instantiated
    if (method_exists($h, 'table')) {
        $cryptocurrencies = $h->table('cryptocurrencies')->select()->orderBy('id', 'desc')->fetchAll();

        if (!empty($cryptocurrencies)) {
            foreach ($cryptocurrencies as $cryptocurrency) {
                // Determine user status
                if ($cryptocurrency['status'] == "active") {
                    $statusView = "<span class='badge badge-light-success'>Active</span>";
                    $userStatus = "<a href='javascript:;' onclick='userStatus(" . $cryptocurrency['id'] . ", 0)' class='btn btn-light-success btn-sm text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-unlock'></i></a>";
                } else if ($cryptocurrency['status'] == "block") {
                    $statusView = "<span class='badge badge-light-danger'>Inactive</span>";
                    $userStatus = "<a href='javascript:;' onclick='userStatus(" . $cryptocurrency['id'] . ", 1)' class='btn-sm btn btn-light-danger text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-lock'></i></a>";
                } else {
                    $statusView = "<span class='badge badge-light-danger'>Inactive</span>";
                    $userStatus = "<a href='javascript:;' onclick='userStatus(" . $cryptocurrency['id'] . ", 1)' class='btn-sm btn btn-light-danger text-start me-2 action-edit' ><i style='font-size: 16px;' class='fa-solid fa-lock'></i></a>";
                }

                // $plus = array("plusView" => "<a class='control' tabindex='0' style=""></a>");
                $action = array('action' => $userStatus . '
                   <a href="javascript:;" class="btn-sm btn btn-light-danger text-start me-2 action-edit" onclick="deleteUser(' . $cryptocurrency["id"] . ')" ><i style="font-size: 16px;" class="fa-regular fa-trash-can"></i></a>
           
                      ');

                $status = array("statusView" => $statusView);
                $srNo++;
                $ids = array("ids" => "$srNo");
                $check_arr[] = array_merge($ids, $cryptocurrency, $status, $action);
            }

            $result = array(
                "sEcho" => 1,
                "iTotalRecords" => count($check_arr),
                "iTotalDisplayRecords" => count($check_arr),
                "aaData" => $check_arr
            );
            echo json_encode($result);
        } else {
            $result = array(
                "sEcho" => 1,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($result);
        }
    } else {
        echo json_encode(array("error" => "Method table not found in the class of \$h."));
    }
}


if ($_GET['page_name'] == "userStatusUpdate") {
//    if (!is_csrf_v_script()) {
//        exit();
//    }
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $status = $_GET['status'];
        $table_name = $_GET['table_name'];
        $status_table = $_GET['status_table'];

        try {
            $statusUpdate = $h->table($table_name)
                ->update([$status_table => $status])
                ->where('id', '=', $id)
                ->run();
            echo "1";
            exit;
        } catch (PDOException $e) {
            echo 0;
            exit;
        }
    }
}
if ($_GET['page_name'] == "delete") {
//    if (!is_csrf_v_script()) {
//        exit();
//    }
    if (isset($_GET['table_name'])) {
        $table_name = $_GET['table_name'];
        $id = $_GET['id'];
        try {
            $h->table($table_name)->delete()->where('id', $id)->run();
            echo "1";
            exit;
        } catch (PDOException $e) {
            echo 0;
            exit;
        }
    }

}

