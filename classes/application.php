<?php

class Application {

    private $db_connect;

    public function __construct() {
        $host_name = 'localhost';
        $user_name = 'root';
        $password = '';
        $db_name = 'db_dothedeals';
        $this->db_connect = mysqli_connect($host_name, $user_name, $password, $db_name);
        if (!$this->db_connect) {
            die('Connection Fail' . mysqli_error($this->db_connect));
        }
    }

    public function select_all_published_category() {
        $sql = 'SELECT * FROM tbl_category WHERE publication_status = 1';
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_all_published_mannufacturer() {
        $sql = 'SELECT * FROM tbl_manufacturer WHERE publication_status = 1';
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_latest_product_info() {
        $sql = 'SELECT * FROM tbl_product WHERE publication_status = 1 ORDER BY product_id DESC LIMIT 6';
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_latest_between_Product($from, $to) {
        $sql = "SELECT * FROM tbl_product WHERE product_price between '$from' and '$to' and publication_status = 1";
        // echo $sql;
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_latest_Product($name) {
        $sql = "SELECT * FROM tbl_product WHERE product_name like '%$name%' or product_short_description like '%$name%'";
        //    echo $sql;
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_all_product_info($id) {
        $sql = "SELECT * FROM tbl_product WHERE product_id='$id'";
        //  echo $sql;
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_product_info_by_category_id($category_id) {
        $sql = "SELECT * FROM tbl_product,tbl_category WHERE tbl_category.category_id=tbl_product.category_id and tbl_category.category_id=$category_id";
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function Save_Review($data) {
        $sql = "INSERT INTO tbl_review (name, email, message, product_id,datetime) "
                . "VALUES ('$data[name]', '$data[email]', '$data[message]', '$data[product_id]',now())";
        if (mysqli_query($this->db_connect, $sql)) {
            $id = $data[product_id];
            $r = mysqli_query($this->db_connect, $sql);
            header('Location: product_details.php?id=' . $id);
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_review_by_id($product_id) {
        $sql = "SELECT * FROM tbl_review WHERE product_id='$product_id' ";
        //echo $sql;
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_product_info_by_id($product_id) {
        $sql = "SELECT p.*, c.category_name, m.manufacturer_name FROM tbl_product as p, tbl_category as c, tbl_manufacturer as m WHERE p.category_id=c.category_id AND p.manufacturer_id=m.manufacturer_id AND product_id='$product_id' ";
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function add_to_cart($data) {
        $product_id = $data['product_id'];
        $sql = "SELECT product_name, product_price, product_image FROM tbl_product WHERE product_id='$product_id' ";
        $query_result = mysqli_query($this->db_connect, $sql);
        $product_info = mysqli_fetch_assoc($query_result);
        session_start();
        $session_id = session_id();

        $sql = "INSERT INTO tbl_temp_cart (session_id, product_id, product_name, product_price, product_quantity, product_image) VALUES ('$session_id', '$product_id', '$product_info[product_name]', '$product_info[product_price]', '$data[product_quantity]', '$product_info[product_image]')";
        mysqli_query($this->db_connect, $sql);

        header('Location: cart.php');
    }

    public function select_cart_product_by_session_id($session_id) {
        $sql = "SELECT * FROM tbl_temp_cart WHERE session_id='$session_id' ";
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function update_cart_product_info($data) {

        $sql = "UPDATE tbl_temp_cart SET product_quantity='$data[product_quantity]' WHERE temp_cart_id='$data[temp_cart_id]' ";
        if (mysqli_query($this->db_connect, $sql)) {
            $message = "Cart product info update successfully";
            return $message;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function delete_cart_product_info_by_id($cart_product_id) {
        $sql = "DELETE FROM tbl_temp_cart WHERE temp_cart_id='$cart_product_id' ";
        if (mysqli_query($this->db_connect, $sql)) {
            $message = "Cart product info delete successfully";
            return $message;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_published_product_info_by_category_id($category_id) {
        $sql = "SELECT * FROM tbl_product WHERE category_id='$category_id' AND publication_status=1 ORDER BY product_id DESC";
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function customer_login($data) {
        $password = md5($data['password']);
        $email = $data['email'];

        $sql = "SELECT * FROM tbl_customer WHERE email_address='$email' AND password='$password' ";
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            $customer_info = mysqli_fetch_assoc($query_result);
//            echo '<pre>';
//            print_r($admin_info);
//            exit();
            if ($customer_info) {
                session_start();
                $_SESSION['customer_name'] = $customer_info['customer_name'];
                $_SESSION['customer_id'] = $customer_info['customer_id'];
                $_SESSION['customer_name'] = $customer_info['first_name'] .' '. $customer_info['last_name'];
                header('Location: shipping.php');
            } else {
                $message = "Your email address or password invalid";
                return $message;
            }
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function save_customer_info($data) {
        $password = md5($data['password']);
        $sql = "INSERT INTO tbl_customer (first_name, last_name, email_address, password, phone_number, blood_group, address, city, district,activation_status) VALUES ('$data[first_name]', '$data[last_name]', '$data[email_address]' ,'$password', '$data[phone_number]', '$data[blood_group]', '$data[address]', '$data[city]', '$data[district]',1 )";
        if (mysqli_query($this->db_connect, $sql)) {
            $customer_id = mysqli_insert_id($this->db_connect);
            //session_start();
            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['customer_name'] = $data['first_name'] . ' ' . $data['last_name'];
            /*  
            $to = $data['email_address'];
            $subject = "Customer email varification";
            $en_customer_id = base64_encode($customer_id);
            $message = "
                <span>Dear $data[last_name]. Thanks for join our community.</span> <br/>
                <span>Your login information goes here.</span> <br/>
                <span>Email Address: </span> $data[email_address]<br/>
                <span>Password: </span> $data[password]<br/>
                <span>To activate your account click on bellow</span><br/>
                <a href='update_customer_status.php?id=$en_customer_id'>update_customer_status.php?id=$en_customer_id</a>    
                    ";
            $headers = 'Form: info@noushin.com';
            //   mail($to, $subject, $message, $headers);
            echo $message;
            exit();
 

*/

             header('Location: shipping.php');
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function update_customer_status($customer_id) {
        $sql = "UPDATE tbl_customer SET activation_status=1 WHERE customer_id='$customer_id' ";
        if (mysqli_query($this->db_connect, $sql)) {

            header('Location: shipping.php');
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function select_customer_info_by_id($customer_id) {
        $sql = "SELECT * FROM tbl_customer WHERE customer_id='$customer_id' ";
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function save_shipping_info($data) {
        $sql = "INSERT INTO tbl_shipping (full_name, email_address, phone_number, address, city, district) VALUES ('$data[full_name]', '$data[email_address]', '$data[phone_number]', '$data[address]', '$data[city]', '$data[district]')";
        if (mysqli_query($this->db_connect, $sql)) {
            $shipping_id = mysqli_insert_id($this->db_connect);
            session_start();
            $_SESSION['shipping_id'] = $shipping_id;

            header('Location: payment.php');
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }
       public function select_my_order_info() {
        $sql="SELECT o.*, c.first_name, c.last_name, p.payment_type, p.payment_status FROM tbl_order as o, tbl_customer as c, tbl_payment as p WHERE o.customer_id=c.customer_id AND o.order_id=p.order_id";
        if(mysqli_query($this->db_connect, $sql)) {
            $query_result=mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem'.  mysqli_error( $this->db_connect) );
        }
    }
    

    public function save_feedback_info($data) {
        $sql = "INSERT INTO tbl_feedback (name, email, feedback, sender_id, type, is_read, datetime)"
                . " VALUES ('$data[name]', '$data[email]', '$data[feedback]','$data[sender_id]', '$data[type]', '$data[is_read]', now())";
        if (mysqli_query($this->db_connect, $sql)) {
            //  $result= mysqli_query($this->db_connect, $sql);
            header('Location: feedback.php?feedbackmessage=Send Feedback Successfully.');
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function save_order_info($data) {
        $payment_type = $data['payment_type'];
        if ($payment_type == 'cash_on_delivery') {
            session_start();
            $customer_id = $_SESSION['customer_id'];
            $shipping_id = $_SESSION['shipping_id'];
            $order_total = $_SESSION['order_total'];
            $sql = "INSERT INTO tbl_order (customer_id, shipping_id, order_total) VALUES ('$customer_id', '$shipping_id', '$order_total')";
            if (mysqli_query($this->db_connect, $sql)) {
                $order_id = mysqli_insert_id($this->db_connect);
                $sql = "INSERT INTO tbl_payment (order_id, payment_type) VALUES ('$order_id', '$payment_type')";
                if (mysqli_query($this->db_connect, $sql)) {
                    $session_id = session_id();
                    $sql = "SELECT * FROM tbl_temp_cart WHERE session_id='$session_id' ";
                    $query_result = mysqli_query($this->db_connect, $sql);
                    while ($product_info = mysqli_fetch_assoc($query_result)) {
                        $sql = "INSERT INTO tbl_order_details (order_id, product_id, product_name, product_price, product_quantity, product_image) VALUES ('$order_id', '$product_info[product_id]', '$product_info[product_name]', '$product_info[product_price]', '$product_info[product_quantity]', '$product_info[product_image]')";
                        mysqli_query($this->db_connect, $sql);
                    }
                    $sql = "DELETE FROM tbl_temp_cart WHERE session_id='$session_id' ";
                    mysqli_query($this->db_connect, $sql);
                    unset($_SESSION['order_total']);

                    header('Location: customer_home.php');
                } else {
                    die('Query problem' . mysqli_error($this->db_connect));
                }
            } else {
                die('Query problem' . mysqli_error($this->db_connect));
            }
        }
    }

    public function customer_email_check($email_address) {
        $sql = "SELECT * FROM tbl_customer WHERE email_address='$email_address' ";
        if (mysqli_query($this->db_connect, $sql)) {
            $query_result = mysqli_query($this->db_connect, $sql);
            return $query_result;
        } else {
            die('Query problem' . mysqli_error($this->db_connect));
        }
    }

    public function customer_logout() {
        unset($_SESSION['customer_id']);
        unset($_SESSION['customer_name']);
        unset($_SESSION['shipping_id']);

        header('Location: index.php');
    }

}
