<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenu du site - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1f2937;
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(135deg, #1f2937, #111827);
            color: white;
            padding: 2rem 0;
        }

        .sidebar-header {
            text-align: center;
            padding: 0 1rem 2rem;
            border-bottom: 1px solid #374151;
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(59, 130, 246, 0.1);
            border-left-color: #3b82f6;
        }

        .sidebar-nav i {
            margin-right: 0.75rem;
            width: 20px;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            max-height: 100vh;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .section-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 2rem;
        }

        .tab {
            padding: 1rem 2rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            color: #6b7280;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .item-list {
            list-style: none;
        }

        .item-card {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .item-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .item-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .item-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
        }

        .item-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .color-picker {
            width: 50px;
            height: 40px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .image-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 0.5rem;
        }

        .logout-btn {
            position: fixed;
            bottom: 2rem;
            left: 2rem;
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 50px;
            height: 50px;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .tabs {
                flex-wrap: wrap;
            }
            
            .tab {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence'; ?></h2>
                <p>Administration</p>
            </div>
            <ul class="sidebar-nav">
                <li><a href="/admin/dashboard">
                    <i class="fas fa-chart-line"></i>
                    Tableau de bord
                </a></li>
                <li><a href="/admin/content" class="active">
                    <i class="fas fa-edit"></i>
                    Contenu du site
                </a></li>
                <li><a href="/admin/contacts">
                    <i class="fas fa-envelope"></i>
                    Messages
                </a></li>
                <li><a href="/admin/schedule">
                    <i class="fas fa-calendar-alt"></i>
                    Planning
                </a></li>
                <li><a href="/admin/settings">
                    <i class="fas fa-cog"></i>
                    Paramètres
                </a></li>
                <li><a href="/" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    Voir le site
                </a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Contenu du site</h1>
                <div class="breadcrumb">Administration / Contenu du site</div>
            </div>

            <!-- Flash Message -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_message']['success'] ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($_SESSION['flash_message']['message']); ?>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" onclick="showTab('content')">Contenu textuel</button>
                <button class="tab" onclick="showTab('services')">Services</button>
                <button class="tab" onclick="showTab('team')">Équipe</button>
                <button class="tab" onclick="showTab('news')">Actualités</button>
                <button class="tab" onclick="showTab('events')">Événements</button>
            </div>

            <!-- Content Tab -->
            <div id="content-tab" class="tab-content active">
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-edit"></i>
                        Modifier le contenu textuel
                    </h2>
                    <form action="/admin/content" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <input type="hidden" name="action" value="update_content">
                        
                        <!-- Hero Section -->
                        <h3>Section Hero</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="hero_title">Titre principal</label>
                                <input type="text" class="form-control" id="hero_title" name="hero[title]" 
                                       value="<?php echo htmlspecialchars($content['hero']['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="hero_subtitle">Sous-titre</label>
                                <textarea class="form-control" id="hero_subtitle" name="hero[subtitle]"><?php echo htmlspecialchars($content['hero']['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- About Section -->
                        <h3>Section À propos</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="about_title">Titre</label>
                                <input type="text" class="form-control" id="about_title" name="about[title]" 
                                       value="<?php echo htmlspecialchars($content['about']['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="about_subtitle">Sous-titre</label>
                                <textarea class="form-control" id="about_subtitle" name="about[subtitle]"><?php echo htmlspecialchars($content['about']['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Services Section -->
                        <h3>Section Services</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="services_title">Titre</label>
                                <input type="text" class="form-control" id="services_title" name="services[title]" 
                                       value="<?php echo htmlspecialchars($content['services']['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="services_subtitle">Sous-titre</label>
                                <textarea class="form-control" id="services_subtitle" name="services[subtitle]"><?php echo htmlspecialchars($content['services']['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Team Section -->
                        <h3>Section Équipe</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="team_title">Titre</label>
                                <input type="text" class="form-control" id="team_title" name="team[title]" 
                                       value="<?php echo htmlspecialchars($content['team']['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="team_subtitle">Sous-titre</label>
                                <textarea class="form-control" id="team_subtitle" name="team[subtitle]"><?php echo htmlspecialchars($content['team']['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- News Section -->
                        <h3>Section Actualités</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="news_title">Titre</label>
                                <input type="text" class="form-control" id="news_title" name="news[title]" 
                                       value="<?php echo htmlspecialchars($content['news']['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="news_subtitle">Sous-titre</label>
                                <textarea class="form-control" id="news_subtitle" name="news[subtitle]"><?php echo htmlspecialchars($content['news']['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Events Section -->
                        <h3>Section Événements</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="events_title">Titre</label>
                                <input type="text" class="form-control" id="events_title" name="events[title]" 
                                       value="<?php echo htmlspecialchars($content['events']['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="events_subtitle">Sous-titre</label>
                                <textarea class="form-control" id="events_subtitle" name="events[subtitle]"><?php echo htmlspecialchars($content['events']['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Values Section -->
                        <h3>Section Valeurs</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="values_title">Titre</label>
                                <input type="text" class="form-control" id="values_title" name="values[title]" 
                                       value="<?php echo htmlspecialchars($content['values']['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="values_subtitle">Sous-titre</label>
                                <textarea class="form-control" id="values_subtitle" name="values[subtitle]"><?php echo htmlspecialchars($content['values']['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Contact Section -->
                        <h3>Section Contact</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="contact_title">Titre</label>
                                <input type="text" class="form-control" id="contact_title" name="contact[title]" 
                                       value="<?php echo htmlspecialchars($content['contact']['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="contact_subtitle">Sous-titre</label>
                                <textarea class="form-control" id="contact_subtitle" name="contact[subtitle]"><?php echo htmlspecialchars($content['contact']['subtitle'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="contact_address">Adresse</label>
                                <textarea class="form-control" id="contact_address" name="contact[address]"><?php echo htmlspecialchars($content['contact']['address'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="contact_phone">Téléphone</label>
                                <input type="text" class="form-control" id="contact_phone" name="contact[phone]" 
                                       value="<?php echo htmlspecialchars($content['contact']['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="contact_email">Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact[email]" 
                                       value="<?php echo htmlspecialchars($content['contact']['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Footer Section -->
                        <h3>Section Footer</h3>
                        <div class="form-group">
                            <label for="footer_copyright">Copyright</label>
                            <input type="text" class="form-control" id="footer_copyright" name="footer[copyright]" 
                                   value="<?php echo htmlspecialchars($content['footer']['copyright'] ?? ''); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Sauvegarder les modifications
                        </button>
                    </form>
                </div>
            </div>

            <!-- Services Tab -->
            <div id="services-tab" class="tab-content">
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-gavel"></i>
                        Gestion des services
                    </h2>
                    
                    <!-- Add Service Form -->
                    <form action="/admin/content" method="POST" style="margin-bottom: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 10px;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <input type="hidden" name="action" value="add_service">
                        <h3>Ajouter un service</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="service_title">Titre</label>
                                <input type="text" class="form-control" id="service_title" name="service_title" required>
                            </div>
                            <div class="form-group">
                                <label for="service_icon">Icône (classe FontAwesome)</label>
                                <input type="text" class="form-control" id="service_icon" name="service_icon" value="fas fa-gavel">
                            </div>
                            <div class="form-group">
                                <label for="service_color">Couleur</label>
                                <input type="color" class="color-picker" id="service_color" name="service_color" value="#3b82f6">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="service_description">Description</label>
                            <textarea class="form-control" id="service_description" name="service_description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="service_detailed_content">Contenu détaillé</label>
                            <textarea class="form-control" id="service_detailed_content" name="service_detailed_content" rows="5"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Ajouter le service
                        </button>
                    </form>

                    <!-- Services List -->
                    <ul class="item-list">
                        <?php foreach ($services as $service): ?>
                        <li class="item-card">
                            <div class="item-header">
                                <div>
                                    <h4 class="item-title">
                                        <i class="<?php echo htmlspecialchars($service['icon']); ?>" style="color: <?php echo htmlspecialchars($service['color']); ?>"></i>
                                        <?php echo htmlspecialchars($service['title']); ?>
                                    </h4>
                                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-primary btn-sm" onclick="editService(<?php echo $service['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                        Modifier
                                    </button>
                                    <form action="/admin/content" method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <input type="hidden" name="action" value="delete_service">
                                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce service ?')">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Edit Form (hidden by default) -->
                            <div id="edit-service-<?php echo $service['id']; ?>" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                <form action="/admin/content" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                    <input type="hidden" name="action" value="update_service">
                                    <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label>Titre</label>
                                            <input type="text" class="form-control" name="service_title" value="<?php echo htmlspecialchars($service['title']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Icône</label>
                                            <input type="text" class="form-control" name="service_icon" value="<?php echo htmlspecialchars($service['icon']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Couleur</label>
                                            <input type="color" class="color-picker" name="service_color" value="<?php echo htmlspecialchars($service['color']); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="service_description" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Contenu détaillé</label>
                                        <textarea class="form-control" name="service_detailed_content" rows="5"><?php echo htmlspecialchars($service['detailed_content'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="service_is_active" <?php echo $service['is_active'] ? 'checked' : ''; ?>>
                                            Service actif
                                        </label>
                                    </div>
                                    <div style="display: flex; gap: 1rem;">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-save"></i>
                                            Sauvegarder
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('service', <?php echo $service['id']; ?>)">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Team Tab -->
            <div id="team-tab" class="tab-content">
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-users"></i>
                        Gestion de l'équipe
                    </h2>
                    
                    <!-- Add Team Member Form -->
                    <form action="/admin/content" method="POST" enctype="multipart/form-data" style="margin-bottom: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 10px;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <input type="hidden" name="action" value="add_team_member">
                        <h3>Ajouter un membre</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="team_name">Nom</label>
                                <input type="text" class="form-control" id="team_name" name="team_name" required>
                            </div>
                            <div class="form-group">
                                <label for="team_position">Poste</label>
                                <input type="text" class="form-control" id="team_position" name="team_position" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="team_description">Description</label>
                            <textarea class="form-control" id="team_description" name="team_description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="team_image">Photo</label>
                            <input type="file" class="form-control" id="team_image" name="team_image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Ajouter le membre
                        </button>
                    </form>

                    <!-- Team List -->
                    <ul class="item-list">
                        <?php foreach ($team as $member): ?>
                        <li class="item-card">
                            <div class="item-header">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <?php if ($member['image_path']): ?>
                                        <img src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                                    <?php endif; ?>
                                    <div>
                                        <h4 class="item-title"><?php echo htmlspecialchars($member['name']); ?></h4>
                                        <p><strong><?php echo htmlspecialchars($member['position']); ?></strong></p>
                                        <p><?php echo htmlspecialchars($member['description']); ?></p>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-primary btn-sm" onclick="editTeamMember(<?php echo $member['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                        Modifier
                                    </button>
                                    <form action="/admin/content" method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <input type="hidden" name="action" value="delete_team_member">
                                        <input type="hidden" name="team_id" value="<?php echo $member['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce membre ?')">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Edit Form -->
                            <div id="edit-team-<?php echo $member['id']; ?>" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                <form action="/admin/content" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                    <input type="hidden" name="action" value="update_team_member">
                                    <input type="hidden" name="team_id" value="<?php echo $member['id']; ?>">
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label>Nom</label>
                                            <input type="text" class="form-control" name="team_name" value="<?php echo htmlspecialchars($member['name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Poste</label>
                                            <input type="text" class="form-control" name="team_position" value="<?php echo htmlspecialchars($member['position']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="team_description" required><?php echo htmlspecialchars($member['description']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Photo</label>
                                        <input type="file" class="form-control" name="team_image" accept="image/*">
                                        <?php if ($member['image_path']): ?>
                                            <img src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="Current" class="image-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="team_is_active" <?php echo $member['is_active'] ? 'checked' : ''; ?>>
                                            Membre actif
                                        </label>
                                    </div>
                                    <div style="display: flex; gap: 1rem;">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-save"></i>
                                            Sauvegarder
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('team', <?php echo $member['id']; ?>)">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- News Tab -->
            <div id="news-tab" class="tab-content">
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-newspaper"></i>
                        Gestion des actualités
                    </h2>
                    
                    <!-- Add News Form -->
                    <form action="/admin/content" method="POST" enctype="multipart/form-data" style="margin-bottom: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 10px;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <input type="hidden" name="action" value="add_news">
                        <h3>Ajouter une actualité</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="news_title">Titre</label>
                                <input type="text" class="form-control" id="news_title" name="news_title" required>
                            </div>
                            <div class="form-group">
                                <label for="news_publish_date">Date de publication</label>
                                <input type="datetime-local" class="form-control" id="news_publish_date" name="news_publish_date" value="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="news_content">Contenu</label>
                            <textarea class="form-control" id="news_content" name="news_content" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="news_image">Image</label>
                            <input type="file" class="form-control" id="news_image" name="news_image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Ajouter l'actualité
                        </button>
                    </form>

                    <!-- News List -->
                    <ul class="item-list">
                        <?php foreach ($news as $item): ?>
                        <li class="item-card">
                            <div class="item-header">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <?php if ($item['image_path']): ?>
                                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 80px; height: 60px; border-radius: 8px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div>
                                        <h4 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h4>
                                        <p><small><?php echo date('d/m/Y H:i', strtotime($item['publish_date'])); ?></small></p>
                                        <p><?php echo htmlspecialchars(substr($item['content'], 0, 100)) . '...'; ?></p>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-primary btn-sm" onclick="editNews(<?php echo $item['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                        Modifier
                                    </button>
                                    <form action="/admin/content" method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <input type="hidden" name="action" value="delete_news">
                                        <input type="hidden" name="news_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette actualité ?')">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Edit Form -->
                            <div id="edit-news-<?php echo $item['id']; ?>" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                <form action="/admin/content" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                    <input type="hidden" name="action" value="update_news">
                                    <input type="hidden" name="news_id" value="<?php echo $item['id']; ?>">
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label>Titre</label>
                                            <input type="text" class="form-control" name="news_title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Date de publication</label>
                                            <input type="datetime-local" class="form-control" name="news_publish_date" value="<?php echo date('Y-m-d\TH:i', strtotime($item['publish_date'])); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Contenu</label>
                                        <textarea class="form-control" name="news_content" rows="5" required><?php echo htmlspecialchars($item['content']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Image</label>
                                        <input type="file" class="form-control" name="news_image" accept="image/*">
                                        <?php if ($item['image_path']): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="Current" class="image-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="news_is_active" <?php echo $item['is_active'] ? 'checked' : ''; ?>>
                                            Actualité active
                                        </label>
                                    </div>
                                    <div style="display: flex; gap: 1rem;">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-save"></i>
                                            Sauvegarder
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('news', <?php echo $item['id']; ?>)">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Events Tab -->
            <div id="events-tab" class="tab-content">
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-calendar"></i>
                        Gestion des événements
                    </h2>
                    
                    <!-- Add Event Form -->
                    <form action="/admin/content" method="POST" enctype="multipart/form-data" style="margin-bottom: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 10px;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <input type="hidden" name="action" value="add_event">
                        <h3>Ajouter un événement</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="event_title">Titre</label>
                                <input type="text" class="form-control" id="event_title" name="event_title" required>
                            </div>
                            <div class="form-group">
                                <label for="event_date">Date de l'événement</label>
                                <input type="datetime-local" class="form-control" id="event_date" name="event_date" value="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="event_content">Description</label>
                            <textarea class="form-control" id="event_content" name="event_content" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="event_image">Image</label>
                            <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Ajouter l'événement
                        </button>
                    </form>

                    <!-- Events List -->
                    <ul class="item-list">
                        <?php foreach ($events as $item): ?>
                        <li class="item-card">
                            <div class="item-header">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <?php if ($item['image_path']): ?>
                                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 80px; height: 60px; border-radius: 8px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div>
                                        <h4 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h4>
                                        <p><small><?php echo date('d/m/Y H:i', strtotime($item['event_date'])); ?></small></p>
                                        <p><?php echo htmlspecialchars(substr($item['content'], 0, 100)) . '...'; ?></p>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-primary btn-sm" onclick="editEvent(<?php echo $item['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                        Modifier
                                    </button>
                                    <form action="/admin/content" method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                        <input type="hidden" name="action" value="delete_event">
                                        <input type="hidden" name="event_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet événement ?')">
                                            <i class="fas fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Edit Form -->
                            <div id="edit-event-<?php echo $item['id']; ?>" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                <form action="/admin/content" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                    <input type="hidden" name="action" value="update_event">
                                    <input type="hidden" name="event_id" value="<?php echo $item['id']; ?>">
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label>Titre</label>
                                            <input type="text" class="form-control" name="event_title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Date de l'événement</label>
                                            <input type="datetime-local" class="form-control" name="event_date" value="<?php echo date('Y-m-d\TH:i', strtotime($item['event_date'])); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="event_content" rows="5" required><?php echo htmlspecialchars($item['content']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Image</label>
                                        <input type="file" class="form-control" name="event_image" accept="image/*">
                                        <?php if ($item['image_path']): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="Current" class="image-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="event_is_active" <?php echo $item['is_active'] ? 'checked' : ''; ?>>
                                            Événement actif
                                        </label>
                                    </div>
                                    <div style="display: flex; gap: 1rem;">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-save"></i>
                                            Sauvegarder
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('event', <?php echo $item['id']; ?>)">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <!-- Logout Button -->
    <button class="logout-btn" onclick="logout()" title="Se déconnecter">
        <i class="fas fa-sign-out-alt"></i>
    </button>

    <script>
        function logout() {
            if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
                window.location.href = '/admin/logout';
            }
        }

        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        function editService(id) {
            document.getElementById('edit-service-' + id).style.display = 'block';
        }

        function editTeamMember(id) {
            document.getElementById('edit-team-' + id).style.display = 'block';
        }

        function editNews(id) {
            document.getElementById('edit-news-' + id).style.display = 'block';
        }

        function editEvent(id) {
            document.getElementById('edit-event-' + id).style.display = 'block';
        }

        function cancelEdit(type, id) {
            document.getElementById('edit-' + type + '-' + id).style.display = 'none';
        }
    </script>
</body>
</html>