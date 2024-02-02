<?
defined('BASEPATH') OR exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class SignupModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function createMember ($createDate, $userName, $password, $nickName, $postalNum, $roadAddress, $jibunAddress, $detailAddress, $extraAddress, $firstName, $lastName, $gender, $birth) {
        // 사용자 입력값 이스케이프 처리
        $userName = htmlspecialchars($userName);
        $password = htmlspecialchars($password);
        $nickName = htmlspecialchars($nickName);
        $postalNum = htmlspecialchars($postalNum);
        $roadAddress = htmlspecialchars($roadAddress);
        $jibunAddress = htmlspecialchars($jibunAddress);
        $detailAddress = htmlspecialchars($detailAddress);
        $extraAddress = htmlspecialchars($extraAddress);
        $firstName = htmlspecialchars($firstName);
        $lastName = htmlspecialchars($lastName);
        $gender = htmlspecialchars($gender);
        $birth = htmlspecialchars($birth);


        // SQL Injection 방어를 위해 Prepared Statements 사용
        $data = array(
            'userName' => $userName, 
            'password' => $password, 
            'createDate' => $createDate,
            'nickName' => $nickName,
            'postalNum' => $postalNum,
            'roadAddress' => $roadAddress,
            'jibunAddress' => $jibunAddress,
            'detailAddress' => $detailAddress,
            'extraAddress' => $extraAddress,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'gender' => $gender,
            'birth' => $birth
        );
        $this->db->insert('member', $data);

        // 삽입된 행의 ID 값을 반환
        return $this->db->insert_id();
    }
}