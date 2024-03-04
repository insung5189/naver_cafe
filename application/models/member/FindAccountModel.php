<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class FindAccountModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }

    public function findMemberEmail($formData)
    {
        $errorData = ['errors' => []];

        try {
            $memberRepo = $this->em->getRepository('Models\Entities\Member');

            $users = $memberRepo->findBy([
                'firstName' => $formData['firstName'],
                'lastName' => $formData['lastName'],
                'phone' => $formData['phone']
            ]);

            if ($users) {
                $emails = array_map(function ($user) {
                    return [
                        'email' => $user->getUserName(),
                        'createDate' => $user->getCreateDate()->format('Y-m-d')
                    ];
                }, $users);

                return [
                    'success' => true,
                    'emails' => $emails
                ];
            } else {
                return ['success' => false, 'errors' => ['message' => '일치하는 사용자 정보를 찾을 수 없습니다.']];
            }
        } catch (\Exception $e) {
            log_message('error', '이메일 찾기 과정 중 오류 발생: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['message' => '이메일 찾기 과정 중 오류가 발생했습니다.']];
        }
    }

    public function validateMember($formData)
    {
        try {
            $queryBuilder = $this->em->createQueryBuilder();

            $query = $queryBuilder->select('m')
                ->from('Models\Entities\Member', 'm')
                ->where('m.userName = :userName')
                ->andWhere('m.firstName = :firstName')
                ->andWhere('m.lastName = :lastName')
                ->andWhere('m.phone = :phone')
                ->setParameter('userName', $formData['userName'])
                ->setParameter('firstName', $formData['firstName'])
                ->setParameter('lastName', $formData['lastName'])
                ->setParameter('phone', $formData['phone'])
                ->getQuery();

            $user = $query->getOneOrNullResult();

            if ($user !== null) {
                return ['success' => true, 'errors' => []];
            } else {
                return ['success' => false, 'errors' => ['message' => '사용자를 찾을 수 없습니다.']];
            }
        } catch (\Exception $e) {
            log_message('error', '회원 검증 실패: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['message' => '사용자를 조회하는 도중 오류가 발생했습니다.']];
        }
    }

    public function updatePassword($userData)
    {
        try {
            $user = $this->em->getRepository('Models\Entities\Member')->find($userData['userId']);
            if (!$user) {
                return ['success' => false, 'errors' => ['notFound' => '사용자를 찾을 수 없습니다.']];
            }

            $regex = '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

            if (!preg_match($regex, $userData['newPassword'])) {
                return ['success' => false, 'errors' => ['valid' => '신규 비밀번호는 영문, 숫자, 특수문자를 포함한 8글자 이상이어야 합니다.']];
            }

            if (password_verify($userData['newPassword'], $user->getPassword())) {
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
