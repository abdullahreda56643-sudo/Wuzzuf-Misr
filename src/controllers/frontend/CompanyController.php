<?php
class CompanyController extends Controller
{
    public function index()
    {
        try {
            $stmt = $this->db()->query(
                "SELECT 
                    c.*,
                    COALESCE(ct.name, g.name, c.address, 'غير محدد') AS location,
                    COALESCE((SELECT COUNT(*) FROM jobs j WHERE j.company_id = c.id AND j.status = 'published'), 0) AS jobs_count,
                    COALESCE((SELECT ROUND(AVG(cr.rating), 1) FROM company_ratings cr WHERE cr.company_id = c.id), 0) AS rating
                 FROM companies c
                 LEFT JOIN cities ct ON c.city_id = ct.id
                 LEFT JOIN governorates g ON c.governorate_id = g.id
                 WHERE c.status = 'active' AND c.verification_status = 'verified'
                 ORDER BY c.created_at DESC"
            );
            $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $companies = [];
        }

        $adSlots = $this->loadAdSlots([
            'header-home',
            'middle-jobs',
            'sidebar',
            'footer',
            'company-header',
        ]);

        $this->view('companies.index', [
            'title' => 'الشركات',
            'companies' => $companies,
            'homeAds' => $adSlots,
        ]);
    }

    public function show(int $id)
    {
        $company = null;

        try {
            $stmt = $this->db()->prepare(
                "SELECT 
                    c.*,
                    ct.name AS city_name,
                    g.name AS governorate_name,
                    COALESCE(ct.name, g.name, c.address, 'غير محدد') AS location
                 FROM companies c
                 LEFT JOIN cities ct ON c.city_id = ct.id
                 LEFT JOIN governorates g ON c.governorate_id = g.id
                 WHERE c.id = ? AND c.status = 'active' AND c.verification_status = 'verified' LIMIT 1"
            );
            $stmt->execute([$id]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $company = null;
        }

        if (!$company) {
            $this->response(404, 'الشركة غير موجودة');
            return;
        }

        $jobs = [];
        try {
            $stmt = $this->db()->prepare(
                "SELECT j.*, ct.name AS city_name, g.name AS governorate_name, 
                        COALESCE(ct.name, g.name, 'غير محدد') AS location, cat.name AS category_name 
                 FROM jobs j
                 INNER JOIN companies c ON c.id = j.company_id
                 LEFT JOIN cities ct ON j.city_id = ct.id
                 LEFT JOIN governorates g ON j.governorate_id = g.id
                 LEFT JOIN job_categories cat ON j.category_id = cat.id
                 WHERE j.company_id = ? AND j.status = 'published' AND c.status = 'active' AND c.verification_status = 'verified'
                 ORDER BY j.created_at DESC 
                 LIMIT 10"
            );
            $stmt->execute([$id]);
            $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $jobs = [];
        }

        $user = current_user();
        $companyRating = new CompanyRating();
        $averageRating = $companyRating->averageByCompany($id);
        $ratingsCount = $companyRating->countByCompany($id);
        $latestReviews = $companyRating->latestByCompany($id, 5);
        $userRating = null;

        if ($user && !empty($user['id'])) {
            $userRating = $companyRating->ratingForUser($id, (int) $user['id']);
        }

        $adSlots = $this->loadAdSlots([
            'header-home',
            'middle-jobs',
            'sidebar',
            'footer',
            'company-header',
        ]);

        $this->view('companies.show', [
            'title' => $company['name'] ?? 'تفاصيل الشركة',
            'company' => $company,
            'jobs' => $jobs,
            'averageRating' => $averageRating,
            'ratingsCount' => $ratingsCount,
            'latestReviews' => $latestReviews,
            'userRating' => $userRating,
            'currentUser' => $user,
            'homeAds' => $adSlots,
        ]);
    }

    public function rate(int $id)
    {
        $user = current_user();
        if (!$user || empty($user['id'])) {
            $this->setFlash('error', 'يجب تسجيل الدخول لتقييم الشركة');
            $this->redirect('/login');
        }

        if ($this->isPost()) {
            $rating = (int) ($this->input('rating', 0));
            $review = trim((string) ($this->input('review', '')));

            if ($rating < 1 || $rating > 5) {
                $this->setFlash('error', 'يرجى اختيار تقييم من 1 إلى 5');
                $this->redirect('/companies/' . $id);
            }

            $companyRating = new CompanyRating();
            $companyRating->upsert($id, (int) $user['id'], $rating, $review);
            $this->setFlash('success', 'تم حفظ تقييمك للشركة بنجاح');
            $this->redirect('/companies/' . $id . '#company-rating');
        }

        $this->redirect('/companies/' . $id);
    }
}

