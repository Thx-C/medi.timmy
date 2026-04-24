<?php
// index.php — Routeur principal MediTimmy

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/RoleMiddleware.php';

$route = $_GET['route'] ?? 'home';

// Routing
match(true) {

    // ── Public ──────────────────────────────────────────────
    $route === 'home'    => (function() {
        if (isset($_SESSION['user'])) redirect('dashboard');
        view('home/index');
    })(),

    $route === 'login'   => (new AuthController())->login(),
    $route === 'logout'  => AuthMiddleware::require(fn() => (new AuthController())->logout()),

    // ── Tous rôles connectés ─────────────────────────────────
    $route === 'dashboard' => AuthMiddleware::require(
        fn() => (new DashboardController())->index()),

    $route === 'settings'  => AuthMiddleware::require(
        fn() => (new SettingsController())->index()),

    $route === 'settings.save' => AuthMiddleware::require(
        fn() => (new SettingsController())->save()),

    // ── Agenda ───────────────────────────────────────────────
    $route === 'agenda'       => AuthMiddleware::require(
        fn() => RoleMiddleware::require('voir_agenda', fn() => (new AgendaController())->index())),

    $route === 'agenda.events'=> AuthMiddleware::require(
        fn() => (new AgendaController())->events()),

    $route === 'agenda.move'  => AuthMiddleware::require(
        fn() => (new AgendaController())->move()),

    $route === 'agenda.create'=> AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_agenda', fn() => (new AgendaController())->create())),

    $route === 'agenda.update'=> AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_agenda', fn() => (new AgendaController())->update())),

    $route === 'agenda.cancel'=> AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_agenda', fn() => (new AgendaController())->cancel())),

    $route === 'mes-rdv'      => AuthMiddleware::require(
        fn() => RoleMiddleware::require('voir_mes_rdv', fn() => (new AgendaController())->mesRdv())),

    // ── Patients ─────────────────────────────────────────────
    $route === 'patients'      => AuthMiddleware::require(
        fn() => RoleMiddleware::require('voir_patients', fn() => (new PatientController())->index())),

    $route === 'patient.search'=> AuthMiddleware::require(
        fn() => RoleMiddleware::require('voir_patients', fn() => (new PatientController())->search())),

    $route === 'patient.show'  => AuthMiddleware::require(
        fn() => RoleMiddleware::require('voir_patients', fn() => (new PatientController())->show())),

    $route === 'patient.create'=> AuthMiddleware::require(
        fn() => RoleMiddleware::require('modifier_patients', fn() => (new PatientController())->create())),

    $route === 'patient.edit'  => AuthMiddleware::require(
        fn() => RoleMiddleware::require('modifier_patients', fn() => (new PatientController())->edit())),

    // ── Dossiers médicaux ────────────────────────────────────
    $route === 'dossiers'      => AuthMiddleware::require(
        fn() => RoleMiddleware::require('voir_dossiers', fn() => (new DossierController())->index())),

    $route === 'dossier.show'  => AuthMiddleware::require(
        fn() => RoleMiddleware::require('voir_dossiers', fn() => (new DossierController())->show())),

    // ── Consultations ────────────────────────────────────────
    $route === 'consultation.create' => AuthMiddleware::require(
        fn() => RoleMiddleware::require('creer_consultation', fn() => (new ConsultationController())->create())),

    // ── Admin ────────────────────────────────────────────────
    $route === 'admin.accounts'       => AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_comptes', fn() => (new AdminController())->accounts())),

    $route === 'admin.account.create' => AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_comptes', fn() => (new AdminController())->createAccount())),

    $route === 'admin.account.toggle' => AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_comptes', fn() => (new AdminController())->toggleUser())),

    $route === 'admin.account.reset'  => AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_comptes', fn() => (new AdminController())->resetPassword())),

    $route === 'admin.account.role'   => AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_comptes', fn() => (new AdminController())->changeRole())),

    $route === 'admin.roles'          => AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_roles', fn() => (new AdminController())->roles())),

    $route === 'admin.role.create'    => AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_roles', fn() => (new AdminController())->createRole())),

    $route === 'admin.role.perms'     => AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_roles', fn() => (new AdminController())->updateRolePerms())),

    $route === 'admin.role.delete'    => AuthMiddleware::require(
        fn() => RoleMiddleware::require('gerer_roles', fn() => (new AdminController())->deleteRole())),

    // ── 404 ──────────────────────────────────────────────────
    default => (function() {
        http_response_code(404);
        view('layouts/404');
    })(),
};
