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

    public function updatePassword($userId, $oldPassword, $newPassword)
    {
        try {
            $user = $this->em->getRepository('Models\Entities\Member')->find($userId);
            if (!$user) {
                throw new Exception('사용자를 찾을 수 없습니다.');
            }

            if (!password_verify($oldPassword, $user->getPassword())) {
                throw new Exception('기존 비밀번호가 일치하지 않습니다.');
            }

            $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
            $this->em->flush();

            return true;
        } catch (Exception $e) {
            log_message('error', '비밀번호 변경 오류: ' . $e->getMessage());
            return false;
        }
    }
}
