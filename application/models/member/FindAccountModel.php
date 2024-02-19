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
        $qb = $this->em->createQueryBuilder();

        // form에서 가져온 데이터로 member 엔티티의 데이터와 일치하는 row를 확인.
        $query = $qb->select('m')
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

        $user = $query->getOneOrNullResult(); // 조건에 맞는 결과가 있으면 해당 엔티티 객체를 반환하고, 결과가 없으면 null을 반환

        return $user;
    }
}
