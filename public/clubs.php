<?php
session_start();
require_once '../backend/data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs | SyncSXC</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sxc-maroon: #0667A4;
            --sxc-gold: #d4af37;
            --white: #ffffff;
            --shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        body { 
            font-family: 'Outfit', sans-serif; 
            background: #fdfdfd; 
            margin: 0; 
            padding: 40px 20px;
        }

        .container { max-width: 1200px; margin: 0 auto; }

        .title-section { text-align: center; margin-bottom: 50px; }
        .title-section h1 { font-size: 2.5rem; color: var(--sxc-maroon); margin-bottom: 10px; }
        .title-section .underline { height: 4px; width: 60px; background: var(--sxc-gold); margin: 0 auto; border-radius: 2px; }

        .club-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        .club-card {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            border: 1px solid #f0f0f0;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .club-card:hover { transform: translateY(-10px); border-color: var(--sxc-gold); }

        .logo-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: #fff;
            padding: 10px;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .club-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .club-code-tag {
            display: inline-block;
            background: #fff8e1;
            color: var(--sxc-gold);
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-transform: uppercase;
            border: 1px solid #ffecb3;
        }

        .club-name { font-size: 1.25rem; font-weight: 700; color: #1a1a1a; margin-bottom: 12px; }
        .club-desc { font-size: 0.9rem; color: #666; line-height: 1.6; margin-bottom: 20px; }

        .club-footer {
            border-top: 1px solid #f5f5f5;
            padding-top: 15px;
            margin-top: auto;
        }

        .club-email {
            font-size: 0.85rem;
            color: var(--sxc-maroon);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 600;
        }

        .club-email i { font-size: 0.9rem; }

        /* Custom scrollbar for better look */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: var(--sxc-maroon); border-radius: 10px; }
    </style>
</head>
<body>

    <div class="container">
        <div class="title-section">
            <h1>Official SXC Clubs</h1>
            <div class="underline"></div>
        </div>

        <div class="club-grid">
            <?php
                $clubs = fetchClubs(); // Fetches from your provided DB structure
                if ($clubs && $clubs->num_rows > 0):
                    while($club = $clubs->fetch_assoc()): 
            ?>
                <div class="club-card">
                    <div>
                        <div class="logo-wrapper">
                            <img src="../img/<?php echo htmlspecialchars($club['logo_path']); ?>" alt="Club Logo" class="club-logo">
                        </div>
                        <span class="club-code-tag"><?php echo htmlspecialchars($club['club_code']); ?></span>
                        <h2 class="club-name"><?php echo htmlspecialchars($club['name']); ?></h2>
                        <p class="club-desc">
                            <?php 
                                // Truncate description for uniform card heights
                                $desc = htmlspecialchars($club['description']);
                                echo (strlen($desc) > 120) ? substr($desc, 0, 117) . '...' : $desc;
                            ?>
                        </p>
                    </div>

                    <div class="club-footer">
                        <a href="mailto:<?php echo htmlspecialchars($club['email']); ?>" class="club-email">
                            <i class="fa-regular fa-envelope"></i>
                            <?php echo htmlspecialchars($club['email']); ?>
                        </a>
                    </div>
                </div>
            <?php 
                    endwhile;
                else:
                    echo "<p style='text-align:center;'>No clubs found in the database.</p>";
                endif; 
            ?>
        </div>
    </div>

</body>
</html>