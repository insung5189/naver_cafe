<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class MypageModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }

    public function updatePassword($userData)
    {
        try {
            $user = $this->em->getRepository('Models\Entities\Member')->find($userData['userId']);
            if (!$user) {
                return ['success' => false, 'errors' => ['notFound' => '사용자를 찾을 수 없습니다.']];
            }

            if (!password_verify($userData['oldPassword'], $user->getPassword())) {
                return ['success' => false, 'errors' => ['valid' => '기존 비밀번호가 일치하지 않습니다.']];
            }

            $regex = '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

            if (!preg_match($regex, $userData['newPassword'])) {
                return ['success' => false, 'errors' => ['valid' => '신규 비밀번호는 영문, 숫자, 특수문자를 포함한 8글자 이상이어야 합니다.']];
            }

            if (password_verify($userData['oldPassword'], $user->getPassword())) {
                return ['success' => false, 'errors' => ['valid' => '기존에 사용하던 비밀번호는 사용하실 수 없습니다.']];
            }

            if ($userData['newPassword'] !== $userData['newPasswordConfirm']) {
                return ['success' => false, 'errors' => ['valid' => '신규 비밀번호와 비밀번호 확인이 일치하지 않습니다.']];
            }

            $user->setPassword(password_hash($userData['newPassword'], PASSWORD_DEFAULT));
            $this->em->flush();

            return ['success' => true, 'errors' => []];
        } catch (\Exception $e) {
            log_message('error', '비밀번호 변경 오류: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['message' => '비밀번호 변경 중 오류가 발생했습니다.']];
        }
    }
}
