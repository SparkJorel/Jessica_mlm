# UI Redesign Roadmap - Jessica TWC Int'l
# Branche : redesign/modern-ui

## Stack technique
- Tailwind CSS 3 (CDN Play)
- Lucide Icons (CDN)
- Google Fonts Inter
- CSS custom jessica-modern.css (variables de marque)
- Mobile First + Material Design principles

## Couleurs de marque (extraites du logo)
- Vert Jessica : #1B7A3D
- Orange globe : #E8621A
- Vert fonce : #0D5A2B

---

## Phase 1 — Foundation (Layout global)

- [x] public/css/jessica-modern.css : Design system complet (variables, composants, responsive)
- [x] templates/base.html.twig : Layout moderne (sidebar collapsible Lucide, header, responsive mobile/desktop)
- [x] templates/security/login.html.twig : Page de connexion split-screen avec logo + formulaire

## Phase 2 — Dashboard

- [x] DashboardController.php : Route /dashboard avec donnees KPI
- [x] templates/dashboard/user.html.twig : Dashboard utilisateur (stats, actions rapides, reseau)
- [x] templates/dashboard/admin.html.twig : Dashboard admin (vue globale, CA, membres, cycles)
- [x] SecurityController.php : Redirection post-login vers /dashboard au lieu de /genealogy

## Phase 3 — Pages de contenu

- [x] Templates list/tableaux (membres, commandes, souscriptions) : cartes + data tables
- [x] Templates formulaires (ajout membre, commande) : inputs Material Design
- [x] Templates bonus (parrainage, binaire, generationnel, indirect) : cartes visuelles
- [x] Templates produits/packs : grilles modernes avec images
- [x] Arbre genealogique : meilleure visualisation responsive
- [x] Pages paiement : UX amelioree (Orange Money / MTN MoMo)
- [x] Templates admin CRUD (produits, memberships, grades, configs, services, etc.)

## Phase 4 — Polish & Responsive

- [x] Flash messages : toasts modernes animes (toast-enter/toast-exit CSS, dismissToast() JS, auto-dismiss 5s)
- [x] Pagination : style uniforme Tailwind (KnpPaginator CSS dans base.html.twig)
- [x] Page profil utilisateur : layout moderne (update_user_profile.html.twig wizard 3 etapes)
- [x] Tests responsive complets (mobile 360px, tablet 768px, desktop 1280px+) — breakpoints Tailwind appliques partout
- [x] Animations et micro-interactions (j-card fade-up, toast animations, hover transitions, page transitions)
- [x] Modal helpers openModal()/closeModal() dans base.html.twig (remplacement Bootstrap modals)
