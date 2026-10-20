<?php

class LocationController extends Controller
{
    public function index()
    {
        $pdo = $this->db();

        $governoratesPerPage = 10;
        $citiesPerPage = 10;

        $governoratesPage = max(1, (int) $this->input('gpage', 1));
        $citiesPage = max(1, (int) $this->input('cpage', 1));

        $totalGovernorates = (int) $pdo->query('SELECT COUNT(*) FROM governorates')->fetchColumn();
        $totalCities = (int) $pdo->query('SELECT COUNT(*) FROM cities')->fetchColumn();

        $governoratesTotalPages = max(1, (int) ceil($totalGovernorates / $governoratesPerPage));
        $citiesTotalPages = max(1, (int) ceil($totalCities / $citiesPerPage));

        $governoratesPage = min($governoratesPage, $governoratesTotalPages);
        $citiesPage = min($citiesPage, $citiesTotalPages);

        $governoratesStmt = $pdo->prepare('SELECT * FROM governorates ORDER BY name ASC LIMIT :limit OFFSET :offset');
        $governoratesStmt->bindValue(':limit', $governoratesPerPage, PDO::PARAM_INT);
        $governoratesStmt->bindValue(':offset', ($governoratesPage - 1) * $governoratesPerPage, PDO::PARAM_INT);
        $governoratesStmt->execute();
        $governorates = $governoratesStmt->fetchAll(PDO::FETCH_ASSOC);

        $citiesStmt = $pdo->prepare('SELECT c.*, g.name AS governorate_name FROM cities c LEFT JOIN governorates g ON g.id = c.governorate_id ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset');
        $citiesStmt->bindValue(':limit', $citiesPerPage, PDO::PARAM_INT);
        $citiesStmt->bindValue(':offset', ($citiesPage - 1) * $citiesPerPage, PDO::PARAM_INT);
        $citiesStmt->execute();
        $cities = $citiesStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.locations.index', [
            'title' => 'إدارة المحافظات والمدن',
            'governorates' => $governorates,
            'cities' => $cities,
            'governoratesPage' => $governoratesPage,
            'governoratesTotalPages' => $governoratesTotalPages,
            'citiesPage' => $citiesPage,
            'citiesTotalPages' => $citiesTotalPages,
        ]);
    }

    public function create()
    {
        $pdo = $this->db();
        $governorates = $pdo->query('SELECT * FROM governorates ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

        if ($this->isPost()) {
            $type = $this->input('type', 'governorate');
            $name = trim((string) $this->input('name', ''));

            if ($name === '') {
                $this->setFlash('error', 'يرجى إدخال اسم الموقع');
                $this->redirect('/admin/locations/create');
            }

            $slug = strtolower(str_replace(' ', '-', $name));

            if ($type === 'city') {
                $governorateId = (int) $this->input('governorate_id', 0);
                if ($governorateId <= 0) {
                    $this->setFlash('error', 'يرجى اختيار المحافظة الخاصة بهذه المدينة');
                    $this->redirect('/admin/locations/create');
                }

                $stmt = $pdo->prepare('INSERT INTO cities (governorate_id, name, slug, created_at) VALUES (?, ?, ?, NOW())');
                $stmt->execute([$governorateId, $name, $slug]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO governorates (name, slug, created_at) VALUES (?, ?, NOW())');
                $stmt->execute([$name, $slug]);
            }

            $this->setFlash('success', 'تم إضافة الموقع بنجاح');
            $this->redirect('/admin/locations');
        }

        $this->view('admin.locations.create', [
            'title' => 'إضافة موقع',
            'governorates' => $governorates,
        ]);
    }
}

