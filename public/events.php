<?php
session_start();
require_once '../backend/data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Timeline | SyncSXC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sxc-maroon: #0667A4;
            --sxc-gold: #d4af37;
            --bg: #f8f9fa;
            --text-main: #2d3436;
            --text-muted: #636e72;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg); 
            color: var(--text-main);
            padding: 40px 20px; 
            margin: 0;
        }

        .header { text-align: center; margin-bottom: 50px; }
        .header h1 { font-weight: 700; color: var(--sxc-maroon); margin-bottom: 10px; }
        .header p { color: var(--text-muted); }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .event-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .event-card:hover { transform: translateY(-5px); }

        /* Status Top Bar */
        .card-header {
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
            border-bottom: 1px solid #eee;
        }

        .status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }

        .price-tag { font-weight: 700; color: var(--sxc-maroon); font-size: 1rem; }

        /* Main Content */
        .card-body { padding: 20px; flex-grow: 1; }
        
        .club-name { 
            color: var(--sxc-gold); 
            font-size: 0.8rem; 
            font-weight: 700; 
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            display: block;
        }

        .event-title { font-size: 1.4rem; margin: 0 0 12px 0; color: #1e272e; }
        .event-desc { font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px; }

        /* Info Grid */
        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            font-size: 0.85rem;
            padding-top: 15px;
            border-top: 1px dashed #eee;
        }

        .info-item { display: flex; align-items: center; gap: 8px; color: #4b5e65; }
        .info-item i { color: var(--sxc-maroon); width: 16px; }

        /* Action Footer */
        .card-footer {
            padding: 15px 20px;
            background: #fdfdfd;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-main { background: var(--sxc-maroon); color: white; }
        .btn-outline { border: 1px solid #ddd; color: var(--text-muted); }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>

    <div class="header">
        <h1>SyncSXC Event Explorer</h1>
        <p>Live updates from your favorite St. Xavier's Clubs</p>
    </div>

    <div class="event-grid">
        <?php
            $events = fetchEvents(); 
            if ($events && $events->num_rows > 0):
                while($ev = $events->fetch_assoc()): 
                    // Logic for Status Colors
                    $statusClass = ($ev['approval_status'] == 'Approved') ? 'status-approved' : 'status-pending';
                    $isTeam = ($ev['is_team_event']) ? 'Team Based' : 'Individual';
                    $priceText = ($ev['price'] > 0) ? 'Rs. ' . $ev['price'] : 'FREE';
        ?>
            <div class="event-card">
                <div class="card-header">
                    <span class="status-pill <?php echo $statusClass; ?>">
                        <?php echo htmlspecialchars($ev['approval_status']); ?>
                    </span>
                    <span class="price-tag"><?php echo $priceText; ?></span>
                </div>

                <div class="card-body">
                    <span class="club-name"><?php echo htmlspecialchars($ev['name']); ?></span>
                    <h2 class="event-title"><?php echo htmlspecialchars($ev['title']); ?></h2>
                    <p class="event-desc"><?php echo htmlspecialchars(substr($ev['description'], 0, 100)) . '...'; ?></p>
                    
                    <div class="info-row">
                        <div class="info-item">
                            <i class="fa-solid fa-calendar-day"></i>
                            <span><?php echo date('M d, Y', strtotime($ev['proposed_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span><?php echo htmlspecialchars($ev['venue']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fa-solid fa-users"></i>
                            <span><?php echo $isTeam; ?> (<?php echo $ev['min_team_size']; ?>-<?php echo $ev['max_team_size']; ?>)</span>
                        </div>
                        <div class="info-item">
                            <i class="fa-solid fa-layer-group"></i>
                            <span><?php echo ($ev['is_multistep']) ? 'Multi-Step' : 'Single Form'; ?></span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="<?php echo $ev['rulebook_url']; ?>" class="btn btn-outline">
                        <i class="fa-solid fa-book"></i> Rules
                    </a>
                    <a href="#" class="btn btn-main">View Details</a>
                </div>
            </div>
        <?php 
                endwhile;
            else:
                echo "<p style='grid-column: 1/-1; text-align:center;'>No events found.</p>";
            endif; 
        ?>
    </div>

</body>
</html>