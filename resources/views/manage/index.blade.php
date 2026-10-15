@extends('manage.layout')

@section('title', 'Gestion - Accueil')
@section('header', 'Carnet de bord')

@section('content')
    <style>
        .journal {
            display: grid;
            gap: 14px;
            position: relative;
            z-index: 3;
        }

        .journal-cover {
            position: relative;
            border: 1px solid rgba(88, 61, 34, .35);
            border-radius: 12px;
            padding: 18px 16px;
            background:
                linear-gradient(180deg, rgba(214, 184, 140, .58), rgba(178, 142, 99, .48)),
                repeating-linear-gradient(90deg, rgba(77, 52, 27, .08) 0 2px, transparent 2px 11px),
                radial-gradient(780px 230px at 8% -12%, rgba(255, 239, 203, .3), transparent 66%);
            box-shadow: 0 12px 26px rgba(65, 42, 22, .2);
            overflow: hidden;
        }

        .journal-cover::after {
            content: "";
            position: absolute;
            right: -22px;
            top: -22px;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(118, 84, 46, .24) 0%, rgba(118, 84, 46, 0) 72%);
            pointer-events: none;
        }

        .journal-title {
            margin: 0;
            color: #3a2614;
            font-family: "Cinzel", "Times New Roman", serif;
            letter-spacing: .04em;
            font-size: clamp(1.25rem, 2vw, 1.7rem);
        }

        .journal-subtitle {
            margin: 7px 0 0;
            color: #5d4326;
            max-width: 760px;
            font-size: .95rem;
            line-height: 1.4;
        }

        .journal-meta {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px dashed rgba(104, 73, 40, .45);
            border-radius: 999px;
            background: rgba(255, 246, 226, .52);
            color: #51371d;
            padding: 5px 11px;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .journal-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(12, 1fr);
        }

        .sheet {
            grid-column: span 12;
            border: 1px solid rgba(114, 84, 49, .3);
            border-radius: 10px;
            background:
                linear-gradient(180deg, rgba(248, 237, 215, .98), rgba(235, 218, 186, .97)),
                repeating-linear-gradient(0deg, transparent 0 29px, rgba(73, 120, 177, .18) 29px 30px);
            color: #322514;
            box-shadow: 0 8px 20px rgba(76, 53, 30, .14);
            padding: 14px;
            position: relative;
        }

        .sheet::before {
            content: "";
            position: absolute;
            left: 24px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(188, 72, 72, .35);
            opacity: .9;
        }

        .sheet-content {
            padding-left: 24px;
        }

        .sheet-title {
            margin: 0 0 8px;
            color: #4a3117;
            font-family: "Segoe Print", "Comic Sans MS", cursive;
            font-size: 1.12rem;
        }

        .stats {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        }

        .sticky {
            border: 1px solid rgba(109, 78, 45, .25);
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 8px 15px rgba(50, 34, 17, .11);
            transform: rotate(var(--tilt));
            min-height: 92px;
        }

        .sticky:nth-child(1) { --tilt: -1deg; background: linear-gradient(180deg, #f8dfa3, #ebc271); }
        .sticky:nth-child(2) { --tilt: .8deg; background: linear-gradient(180deg, #c6e2d5, #9dc9b7); }
        .sticky:nth-child(3) { --tilt: -0.7deg; background: linear-gradient(180deg, #c6d7ef, #a5bee2); }
        .sticky:nth-child(4) { --tilt: .9deg; background: linear-gradient(180deg, #e8c8c8, #dca6a6); }

        .sticky-label {
            color: #5f4628;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .sticky-value {
            margin-top: 6px;
            color: #2e2113;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.95rem;
            line-height: 1;
        }

        .columns {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .entry-list {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        .entry-list li + li {
            margin-top: 5px;
        }

        .ledger {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .ledger th,
        .ledger td {
            border-bottom: 1px dashed rgba(102, 73, 41, .34);
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
            color: #3f2d1a;
        }

        .ledger th {
            color: #674620;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-size: .78rem;
            font-family: "Cinzel", "Times New Roman", serif;
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            padding-left: 24px;
        }

        .empty-note {
            margin: 6px 0 0;
            color: #6d5234;
            font-size: .92rem;
            font-style: italic;
        }

        .dashboard-hero {
            padding: 14px 16px 12px;
        }

        .dashboard-hero::before,
        .dashboard-hero::after {
            display: none;
        }

        .dashboard-hero-top {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(280px, .95fr);
            gap: 12px;
            align-items: end;
        }

        .dashboard-world {
            display: grid;
            gap: 6px;
            align-content: start;
        }

        .active-world {
            display: grid;
            gap: 4px;
            margin-top: 8px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(101, 73, 40, .16);
            background: rgba(255, 255, 255, .42);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.45);
            max-width: 100%;
        }

        .active-world-label {
            color: rgba(247, 236, 214, .74);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .active-world-name {
            color: #fff4df;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.2rem;
            line-height: 1.15;
        }

        .active-world-meta {
            color: rgba(247, 236, 214, .82);
            font-size: .84rem;
            line-height: 1.4;
        }

        .dashboard-search {
            display: grid;
            gap: 8px;
        }

        .dashboard-search .field {
            margin: 0;
        }

        .dashboard-search input[type="search"] {
            width: 100%;
        }

        .dashboard-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 4px;
        }

        .action-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #3a2614;
            border: 1px solid rgba(104, 73, 40, .14);
            border-radius: 999px;
            padding: 10px 14px;
            background: linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(247, 238, 222, .92));
            box-shadow: 0 10px 20px rgba(75, 47, 22, .09);
            transition: transform .14s ease, box-shadow .14s ease, background .14s ease;
        }

        .action-pill:hover {
            transform: translateY(-1px);
            background: rgba(255,255,255,.98);
            box-shadow: 0 14px 24px rgba(75, 47, 22, .14);
        }

        .action-icon {
            display: inline-grid;
            place-items: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(180deg, #f4c86f, #e0a83f);
            color: #fff;
            font-size: .82rem;
            flex: 0 0 auto;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 16px;
        }

        .dashboard-card {
            --card-accent: #d1a24b;
            border: 1px solid rgba(114, 84, 49, .16);
            border-radius: 22px;
            background:
                radial-gradient(900px 180px at 20% 0%, rgba(255, 255, 255, .88), transparent 56%),
                linear-gradient(180deg, rgba(255, 252, 245, .96), rgba(244, 231, 206, .95));
            box-shadow: 0 18px 34px rgba(76, 53, 30, .1);
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--card-accent), rgba(255, 255, 255, 0));
            opacity: .96;
        }

        .dashboard-card::after {
            content: "";
            position: absolute;
            right: 16px;
            bottom: 12px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, .22) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
        }

        .dashboard-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }

        .dashboard-card-title {
            margin: 0;
            color: #382514;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.08rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .dashboard-card-subtitle {
            margin: 0;
            color: #70553a;
            font-size: .86rem;
        }

        .dashboard-mini {
            display: grid;
            gap: 8px;
        }

        .dashboard-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
        }

        .dashboard-list li {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            min-width: 0;
        }

        .dashboard-list a {
            color: inherit;
            text-decoration: none;
        }

        .dashboard-list a:hover {
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .dashboard-note {
            color: #70553a;
            font-style: italic;
            margin: 0;
        }

        .dashboard-grid .span-6 { grid-column: span 6; }
        .dashboard-grid .span-4 { grid-column: span 4; }
        .dashboard-grid .span-8 { grid-column: span 8; }
        .dashboard-grid .span-12 { grid-column: span 12; }

        .stat-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .stat-card {
            border: 1px solid rgba(109, 78, 45, .12);
            border-radius: 18px;
            padding: 14px;
            min-height: 138px;
            background: rgba(255,255,255,.64);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 18px rgba(50, 34, 17, .08);
        }

        .stat-card:nth-child(1) { background: linear-gradient(180deg, rgba(252, 232, 182, .96), rgba(241, 208, 122, .92)); }
        .stat-card:nth-child(2) { background: linear-gradient(180deg, rgba(210, 237, 225, .96), rgba(168, 214, 198, .92)); }
        .stat-card:nth-child(3) { background: linear-gradient(180deg, rgba(212, 224, 246, .96), rgba(179, 201, 232, .92)); }
        .stat-card:nth-child(4) { background: linear-gradient(180deg, rgba(244, 213, 213, .96), rgba(229, 179, 179, .92)); }

        .stat-top {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
        }

        .stat-label {
            color: #5f4628;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .75rem;
        }

        .stat-value {
            margin-top: 6px;
            color: #2e2113;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 2rem;
            line-height: 1;
        }

        .stat-delta {
            font-size: .78rem;
            color: #5d4326;
        }

        .stat-delta.positive { color: #2d6a4f; }
        .stat-delta.negative { color: #8a3b3b; }
        .stat-delta.neutral { color: #6d5234; }

        .stat-latest {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed rgba(101, 74, 42, .2);
            color: #5d4326;
            font-size: .84rem;
            line-height: 1.35;
        }

        .stat-latest strong {
            display: block;
            color: #3a2614;
            font-size: .9rem;
            margin-bottom: 2px;
        }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 12px;
        }

        .section-grid .span-3 { grid-column: span 3; }
        .section-grid .span-4 { grid-column: span 4; }
        .section-grid .span-6 { grid-column: span 6; }
        .section-grid .span-8 { grid-column: span 8; }
        .section-grid .span-12 { grid-column: span 12; }

        .progress-stack {
            display: grid;
            gap: 10px;
        }

        .progress-row {
            display: grid;
            gap: 4px;
        }

        .progress-row-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: #4a3117;
            font-size: .86rem;
        }

        .progress-bar {
            height: 8px;
            border-radius: 999px;
            background: rgba(255,255,255,.52);
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,.08);
        }

        .progress-bar > span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #f2c76e, #9dc9b7);
        }

        .completion-item {
            display: grid;
            gap: 4px;
            padding: 8px 0;
        }

        .completion-item + .completion-item {
            border-top: 1px dashed rgba(102, 73, 41, .18);
        }

        .completion-title {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            color: #3a2614;
            font-size: .9rem;
        }

        .completion-title a {
            color: inherit;
            text-decoration: none;
        }

        .completion-title a:hover {
            text-decoration: underline;
        }

        .completion-missing {
            color: #6d5234;
            font-size: .8rem;
        }

        .search-section {
            margin-top: 2px;
        }

        .search-results {
            display: grid;
            gap: 10px;
        }

        .search-group {
            padding-top: 4px;
        }

        .search-group h4 {
            margin: 0 0 8px;
            color: #4a3117;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: .9rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding-left: 0;
        }

        .search-result-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            padding: 8px 0 8px 24px;
            border-top: 1px dashed rgba(102, 73, 41, .18);
        }

        .search-result-main {
            min-width: 0;
        }

        .search-result-title {
            margin: 0;
            color: #3a2614;
            font-weight: 700;
        }

        .search-result-meta {
            color: #6d5234;
            font-size: .82rem;
            line-height: 1.35;
        }

        .search-result-link {
            flex: 0 0 auto;
        }

        .badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            padding-left: 24px;
        }

        .badge-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(104, 73, 40, .16);
            background: rgba(255, 255, 255, .58);
            color: #3a2614;
            font-size: .82rem;
        }

        .badge-chip strong {
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: .95rem;
        }

        .activity-item {
            display: grid;
            gap: 3px;
            padding: 8px 0;
            border-top: 1px dashed rgba(102, 73, 41, .18);
        }

        .activity-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .activity-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .66);
            border: 1px solid rgba(104, 73, 40, .14);
            color: #4a3117;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .activity-title {
            color: #3a2614;
            font-weight: 700;
        }

        .activity-meta {
            color: #6d5234;
            font-size: .82rem;
            line-height: 1.35;
        }

        .progress-summary {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
            padding-left: 24px;
            margin-bottom: 10px;
        }

        .progress-overall {
            color: #2e2113;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 2rem;
            line-height: 1;
        }

        .progress-overall small {
            font-size: .8rem;
            color: #6d5234;
            font-family: "Garamond", "Georgia", serif;
        }

        .birthday-blast {
            position: relative;
            border: 1px solid rgba(113, 74, 34, .35);
            border-radius: 18px;
            padding: 18px;
            background:
                radial-gradient(700px 220px at 50% 0%, rgba(255, 248, 207, .95), transparent 58%),
                radial-gradient(520px 180px at 10% 0%, rgba(255, 180, 92, .36), transparent 62%),
                linear-gradient(180deg, rgba(255, 236, 194, .98), rgba(245, 208, 154, .9));
            box-shadow: 0 18px 34px rgba(99, 62, 24, .22);
            overflow: hidden;
            animation: birthday-rise 900ms cubic-bezier(.16, .84, .27, 1) both, birthday-glow 4s ease-in-out infinite;
        }

        .birthday-blast::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                repeating-linear-gradient(135deg, rgba(255,255,255,.12) 0 12px, rgba(0,0,0,0) 12px 24px),
                radial-gradient(circle at 50% 2%, rgba(255,255,255,.58) 0 2px, transparent 3px),
                radial-gradient(circle at 18% 14%, rgba(255,255,255,.42) 0 1.5px, transparent 3px),
                radial-gradient(circle at 82% 12%, rgba(255,255,255,.42) 0 1.5px, transparent 3px);
            opacity: .55;
            z-index: 0;
        }

        .birthday-blast::before {
            content: "";
            position: absolute;
            inset: 10px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, .18);
            box-shadow: inset 0 0 0 1px rgba(131, 87, 31, .08);
            pointer-events: none;
            z-index: 0;
        }

        .birthday-head {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .birthday-head-left {
            min-width: 0;
            display: grid;
            gap: 2px;
        }

        .birthday-head-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .birthday-title {
            margin: 0;
            color: #4b250c;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: clamp(1.35rem, 2.8vw, 2.35rem);
            text-shadow:
                0 1px 0 rgba(255,255,255,.8),
                0 4px 16px rgba(120, 68, 15, .12);
            letter-spacing: .03em;
            animation: birthday-pop 1s ease both, birthday-wiggle 5.5s ease-in-out infinite 1.1s;
        }

        .birthday-sub {
            margin: 4px 0 0;
            color: #7a562d;
            font-size: .94rem;
        }

        .birthday-cake {
            width: 72px;
            height: 54px;
            border-radius: 12px 12px 10px 10px;
            background:
                linear-gradient(180deg, #f8d2dc 0 30%, #fff0bf 30% 61%, #f0bf88 61% 100%);
            border: 1px solid rgba(101, 63, 27, .35);
            box-shadow: 0 10px 18px rgba(82, 50, 20, .24);
            position: relative;
            flex: 0 0 auto;
            animation: birthday-float 2.8s ease-in-out infinite;
        }

        .birthday-cake::before {
            content: "";
            position: absolute;
            width: 10px;
            height: 18px;
            left: 31px;
            top: -16px;
            background: linear-gradient(180deg, #ffe9a9, #f6be43);
            border-radius: 4px;
            border: 1px solid rgba(104, 66, 28, .3);
            animation: candle-flicker .95s ease-in-out infinite alternate;
        }

        .birthday-cake::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            left: 28px;
            top: -28px;
            background: radial-gradient(circle, #fffbe0 0, #ffd66f 40%, #ff9f24 68%, rgba(255, 159, 36, 0) 72%);
            border-radius: 50%;
            box-shadow: 0 0 16px rgba(255, 174, 50, .85);
            animation: flame-bloom .95s ease-in-out infinite alternate;
        }

        .birthday-music-btn {
            border: 1px solid rgba(88, 57, 23, .35);
            border-radius: 999px;
            padding: 7px 12px;
            background: rgba(255, 255, 255, .75);
            color: #4a2f16;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(75, 47, 22, .14);
        }

        .birthday-music-btn:hover {
            background: rgba(255, 255, 255, .9);
        }

        .birthday-list {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            padding-top: 2px;
        }

        .birthday-card {
            display: flex;
            gap: 10px;
            align-items: center;
            border: 1px solid rgba(111, 73, 35, .24);
            border-radius: 16px;
            background:
                radial-gradient(240px 110px at 12% 0%, rgba(255,255,255,.9), transparent 62%),
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(252,245,229,.9));
            padding: 12px 13px;
            text-decoration: none;
            color: #3b2815;
            box-shadow: 0 10px 18px rgba(81, 51, 22, .14);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
            position: relative;
            overflow: hidden;
            animation: birthday-card-in 700ms cubic-bezier(.18, .86, .28, 1) both;
        }

        .birthday-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(180deg, #c77d2b, #8f4f12, #d95f9b);
            opacity: .85;
        }

        .birthday-card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 16px 26px rgba(81, 51, 22, .2);
            filter: saturate(1.06);
        }

        .birthday-photo-wrap {
            position: relative;
            width: 72px;
            height: 72px;
            flex: 0 0 72px;
            animation: birthday-orbit 5.8s ease-in-out infinite;
        }

        .birthday-photo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center 18%;
            border: 3px solid rgba(90, 59, 27, .35);
            background: #f5ead8;
            box-shadow: 0 6px 14px rgba(56, 34, 12, .18);
        }

        .birthday-fallback {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 3px solid rgba(90, 59, 27, .35);
            display: grid;
            place-items: center;
            background: linear-gradient(180deg, #f7ebd4, #e9d0a2);
            color: #6b471f;
            font-weight: 800;
            box-shadow: 0 6px 14px rgba(56, 34, 12, .18);
        }

        .party-hat {
            position: absolute;
            width: 24px;
            height: 26px;
            left: 48px;
            top: -6px;
            clip-path: polygon(50% 0, 0 100%, 100% 100%);
            background: linear-gradient(180deg, #7b4cd5, #d35fa7);
            border: 1px solid rgba(49, 27, 98, .45);
            transform: rotate(16deg);
            box-shadow: 0 5px 10px rgba(0, 0, 0, .16);
            z-index: 3;
            animation: hat-bounce 1.8s ease-in-out infinite;
        }

        .party-hat::after {
            content: "";
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ffeaa5;
            top: -6px;
            left: 9px;
            border: 1px solid rgba(119, 84, 24, .35);
        }

        .birthday-name {
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.08rem;
            color: #4d2f14;
        }

        .birthday-world {
            color: #6a4d30;
            font-size: .84rem;
        }

        .confetti-canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .birthday-stage {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
        }

        .birthday-ribbon {
            position: absolute;
            width: 140px;
            height: 10px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(214, 88, 128, .25), rgba(243, 195, 98, .75), rgba(120, 79, 206, .32));
            box-shadow: 0 0 18px rgba(255, 255, 255, .2);
            opacity: .95;
            filter: blur(.1px);
            animation: ribbon-wave 3.6s ease-in-out infinite;
        }

        .birthday-ribbon.left {
            top: 24px;
            left: 12px;
            transform: rotate(-12deg);
        }

        .birthday-ribbon.right {
            top: 28px;
            right: 8%;
            width: 118px;
            transform: rotate(15deg);
            animation-delay: .6s;
        }

        .birthday-balloon {
            position: absolute;
            width: 34px;
            height: 42px;
            border-radius: 50% 50% 46% 46%;
            box-shadow: inset -8px -14px 16px rgba(0,0,0,.14), 0 8px 14px rgba(74, 41, 14, .14);
            transform-origin: center bottom;
            animation: balloon-float 4.8s ease-in-out infinite;
        }

        .birthday-balloon::before {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -18px;
            width: 1px;
            height: 18px;
            background: rgba(121, 79, 36, .42);
        }

        .birthday-balloon::after {
            content: "";
            position: absolute;
            width: 9px;
            height: 12px;
            left: 12px;
            bottom: -3px;
            border-radius: 0 0 12px 12px;
            background: inherit;
            filter: brightness(.92);
            transform: skewX(8deg);
        }

        .birthday-balloon.one {
            top: 44px;
            left: 10px;
            background: linear-gradient(180deg, #ff8fa5, #d95872);
            animation-delay: .2s;
        }

        .birthday-balloon.two {
            top: 20px;
            right: 86px;
            background: linear-gradient(180deg, #7fd6c1, #3aa28e);
            animation-delay: .8s;
            width: 30px;
            height: 38px;
        }

        .birthday-balloon.three {
            top: 58px;
            right: 28px;
            background: linear-gradient(180deg, #f4d36d, #d79c32);
            animation-delay: 1.1s;
            width: 28px;
            height: 36px;
        }

        .birthday-sparkle {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: radial-gradient(circle, #fffadf 0, #fff3a1 35%, rgba(255, 243, 161, 0) 75%);
            box-shadow: 0 0 14px rgba(255, 242, 164, .85);
            animation: sparkle-twinkle 1.8s ease-in-out infinite;
        }

        .birthday-sparkle.a { top: 20px; left: 36%; }
        .birthday-sparkle.b { top: 66px; left: 58%; animation-delay: .5s; }
        .birthday-sparkle.c { top: 26px; right: 16%; animation-delay: 1.1s; }
        .birthday-sparkle.d { bottom: 22px; left: 20%; animation-delay: .7s; }

        .birthday-badge {
            position: absolute;
            right: 16px;
            bottom: 14px;
            z-index: 2;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .8);
            color: #7b4a16;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            box-shadow: 0 8px 14px rgba(86, 50, 13, .12);
            animation: badge-pulse 2.2s ease-in-out infinite;
        }

        @keyframes birthday-rise {
            from {
                opacity: 0;
                transform: translateY(14px) scale(.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes birthday-glow {
            0%, 100% { box-shadow: 0 18px 34px rgba(99, 62, 24, .22); }
            50% { box-shadow: 0 20px 42px rgba(141, 86, 28, .28); }
        }

        @keyframes birthday-pop {
            0% { transform: scale(.82); opacity: 0; }
            70% { transform: scale(1.04); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes birthday-wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-.7deg); }
            50% { transform: rotate(.9deg); }
            75% { transform: rotate(-.4deg); }
        }

        @keyframes birthday-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        @keyframes candle-flicker {
            0% { transform: translateX(0) scaleY(.95); filter: brightness(.98); }
            100% { transform: translateX(1px) scaleY(1.05); filter: brightness(1.08); }
        }

        @keyframes flame-bloom {
            0% { transform: scale(.88); }
            100% { transform: scale(1.12); }
        }

        @keyframes birthday-card-in {
            from {
                opacity: 0;
                transform: translateY(12px) scale(.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes birthday-orbit {
            0%, 100% { transform: rotate(-1deg) translateY(0); }
            50% { transform: rotate(1deg) translateY(-3px); }
        }

        @keyframes hat-bounce {
            0%, 100% { transform: rotate(16deg) translateY(0); }
            50% { transform: rotate(14deg) translateY(-3px); }
        }

        @keyframes ribbon-wave {
            0%, 100% { transform: translateX(0) rotate(-12deg) scaleX(1); }
            50% { transform: translateX(6px) rotate(-10deg) scaleX(1.03); }
        }

        @keyframes balloon-float {
            0%, 100% { transform: translateY(0) rotate(-1deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
        }

        @keyframes sparkle-twinkle {
            0%, 100% { transform: scale(.8); opacity: .55; }
            50% { transform: scale(1.2); opacity: 1; }
        }

        @keyframes badge-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @media (prefers-reduced-motion: reduce) {
            .birthday-blast,
            .birthday-title,
            .birthday-cake,
            .birthday-card,
            .birthday-photo-wrap,
            .party-hat,
            .birthday-ribbon,
            .birthday-balloon,
            .birthday-sparkle,
            .birthday-badge {
                animation: none !important;
            }
        }

        @media (max-width: 980px) {
            .columns {
                grid-template-columns: 1fr;
            }

            .birthday-head {
                align-items: flex-start;
            }

            .birthday-head-actions {
                width: 100%;
                justify-content: space-between;
            }

            .birthday-music-btn {
                font-size: .78rem;
            }

            .birthday-badge {
                right: 12px;
                bottom: 12px;
            }
        }

        /* Visual refresh */
        body::before {
            filter: blur(6px) saturate(.9) contrast(.94);
            opacity: .14;
        }

        body::after {
            background:
                radial-gradient(1100px 420px at 50% 0%, rgba(255, 231, 176, .14), transparent 72%),
                radial-gradient(900px 260px at 10% 10%, rgba(255, 255, 255, .06), transparent 60%),
                repeating-linear-gradient(0deg, rgba(255,255,255,0.02) 0 1px, transparent 1px 6px);
        }

        .wrap {
            border-radius: 20px;
            border-color: rgba(228, 191, 127, .24);
            background: rgba(20, 25, 35, .72);
            box-shadow: 0 34px 100px rgba(6, 9, 14, .54);
        }

        .sidebar {
            background:
                linear-gradient(180deg, rgba(248, 239, 219, .98), rgba(229, 210, 175, .98)),
                repeating-linear-gradient(0deg, transparent 0 28px, rgba(0, 0, 0, .18) 28px 29px),
                linear-gradient(90deg, rgba(0, 0, 0, .26) 0 2px, transparent 2px 100%);
            padding: 18px 12px 20px;
        }

        .brand {
            margin-bottom: 14px;
            padding: 6px 8px 12px;
        }

        .brand-logo {
            height: 86px;
            max-width: 230px;
            filter: drop-shadow(0 3px 6px rgba(73, 52, 26, .1));
        }

        .nav-title {
            margin: 12px 10px 8px;
            color: #7a5e3a;
        }

        .nav-link {
            padding: 8px 10px;
            border-radius: 12px;
            margin-bottom: 6px;
            transition: transform 140ms ease, background 140ms ease, box-shadow 140ms ease;
        }

        .nav-link:hover {
            transform: translateX(4px);
            background: rgba(255, 255, 255, .46);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.55);
            text-decoration: none;
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(255,255,255,.56), rgba(255,255,255,.18));
            box-shadow: inset 0 0 0 1px rgba(110, 81, 47, .08);
        }

        .btn-mini {
            border-radius: 999px;
            border-color: rgba(89, 65, 37, .22);
            background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(243, 234, 216, .92));
            box-shadow: 0 6px 12px rgba(64, 41, 19, .08);
        }

        .btn-mini.secondary {
            background: linear-gradient(180deg, rgba(208, 221, 242, .96), rgba(181, 200, 231, .96));
        }

        header {
            padding: 16px 20px;
            border-bottom-color: rgba(228, 191, 127, .24);
            background:
                linear-gradient(180deg, rgba(27, 33, 46, .94), rgba(21, 27, 38, .92)),
                radial-gradient(620px 180px at 50% -10%, rgba(255, 222, 150, .2), transparent 70%);
            backdrop-filter: blur(12px);
        }

        .title {
            font-family: "Cinzel", "Georgia", serif;
            font-weight: 700;
            letter-spacing: .03em;
            text-shadow: 0 1px 0 rgba(0,0,0,.2);
        }

        .topline {
            color: rgba(246, 232, 203, .82);
        }

        .journal {
            gap: 18px;
            padding: 2px 2px 10px;
        }

        .dashboard-hero,
        .dashboard-card {
            border-radius: 22px;
            border: 1px solid rgba(124, 95, 60, .14);
            background:
                radial-gradient(900px 180px at 20% 0%, rgba(255, 255, 255, .88), transparent 56%),
                linear-gradient(180deg, rgba(255, 252, 245, .96), rgba(244, 231, 206, .95));
            box-shadow: 0 18px 34px rgba(90, 61, 31, .11);
        }

        .dashboard-hero {
            --card-accent: #b98a4a;
        }

        .dashboard-card--stats {
            --card-accent: #d1a24b;
        }

        .dashboard-card--activity {
            --card-accent: #7aaea7;
        }

        .dashboard-card--shortcuts {
            --card-accent: #8ea6d6;
        }

        .dashboard-card--progress {
            --card-accent: #d3a56a;
        }

        .dashboard-card--completion {
            --card-accent: #d59393;
        }

        .dashboard-hero {
            padding: 20px 22px 18px;
            overflow: hidden;
        }

        .dashboard-hero::before {
            content: "";
            display: block;
            position: absolute;
            inset: 1px;
            border-radius: 21px;
            background:
                radial-gradient(420px 160px at 8% 0%, rgba(255, 255, 255, .6), transparent 60%),
                radial-gradient(360px 180px at 100% 0%, rgba(232, 203, 147, .42), transparent 58%);
            opacity: .8;
            pointer-events: none;
        }

        .dashboard-hero::after {
            content: "";
            display: block;
            position: absolute;
            inset: auto 14px 14px auto;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(238, 206, 132, .22), rgba(238, 206, 132, 0) 72%);
            pointer-events: none;
        }

        .dashboard-card::before {
            background: linear-gradient(90deg, var(--card-accent), rgba(255, 255, 255, 0));
        }

        .dashboard-hero-top {
            gap: 16px;
            align-items: stretch;
        }

        .dashboard-world,
        .dashboard-card-head,
        .search-group h4,
        .search-result-item,
        .badge-row,
        .dashboard-note,
        .stat-grid,
        .progress-summary,
        .progress-stack,
        .completion-item,
        .activity-item {
            position: relative;
            z-index: 1;
        }

        .journal-meta {
            border-style: solid;
            background: linear-gradient(180deg, rgba(255,255,255,.72), rgba(245, 230, 197, .7));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.6);
        }

        .journal-title {
            color: #3d2715;
            font-size: clamp(1.7rem, 3vw, 2.55rem);
            text-shadow: 0 1px 0 rgba(255,255,255,.55);
        }

        .journal-subtitle {
            max-width: 62ch;
            color: #5d4427;
            font-size: .98rem;
            line-height: 1.65;
        }

        .dashboard-actions {
            margin-top: 12px;
            gap: 10px;
        }

        .action-pill {
            padding: 10px 14px;
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(246, 238, 225, .88));
        }

        .action-pill:hover {
            box-shadow: 0 12px 20px rgba(75, 47, 22, .14);
        }

        .action-icon {
            width: 26px;
            height: 26px;
        }

        .active-world {
            padding: 16px;
            border-radius: 18px;
            border-color: rgba(228, 191, 127, .2);
            background:
                radial-gradient(500px 160px at 20% 0%, rgba(255, 255, 255, .08), transparent 62%),
                linear-gradient(180deg, rgba(25, 30, 42, .96), rgba(39, 44, 57, .94));
            box-shadow: 0 16px 32px rgba(26, 20, 12, .2);
            color: #f7ecd7;
        }

        .active-world-name {
            font-size: 1.25rem;
        }

        .badge-row {
            gap: 10px;
            padding-left: 0;
            margin-top: 12px;
        }

        .badge-chip {
            background: rgba(255,255,255,.12);
            border-color: rgba(228, 191, 127, .18);
            color: #f7ecd7;
        }

        .dashboard-search {
            gap: 10px;
            margin-top: 8px;
        }

        .dashboard-search input[type="search"] {
            height: 48px;
            border-radius: 14px;
            border: 1px solid rgba(106, 77, 43, .18);
            background: rgba(255, 253, 249, .95);
            padding: 0 14px;
            font-size: .95rem;
            color: #3b2815;
            box-shadow: inset 0 1px 2px rgba(0,0,0,.04), 0 10px 20px rgba(75, 47, 22, .05);
        }

        .dashboard-search input[type="search"]:focus {
            outline: none;
            border-color: rgba(199, 125, 43, .48);
            box-shadow: 0 0 0 4px rgba(234, 188, 102, .18), 0 10px 20px rgba(75, 47, 22, .05);
        }

        .search-results {
            gap: 12px;
        }

        .search-group h4,
        .dashboard-card-title {
            color: #4a3117;
        }

        .search-result-item {
            padding: 11px 12px 11px 18px;
            border-top: 1px solid rgba(102, 73, 41, .12);
            border-radius: 12px;
            background: rgba(255,255,255,.45);
        }

        .stat-grid {
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        }

        .stat-card {
            min-height: 150px;
            border-radius: 14px;
            border-color: rgba(109, 78, 45, .14);
            box-shadow: 0 10px 18px rgba(50, 34, 17, .1);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 24px rgba(50, 34, 17, .14);
        }

        .stat-value {
            font-size: 2.15rem;
        }

        .stat-latest {
            margin-top: 12px;
            padding-top: 12px;
        }

        .dashboard-grid {
            gap: 14px;
            align-items: start;
        }

        .dashboard-grid .span-8 { grid-column: span 8; }
        .dashboard-grid .span-4 { grid-column: span 4; }

        .dashboard-stack {
            display: grid;
            gap: 14px;
        }

        .dashboard-stack .dashboard-card {
            height: 100%;
        }

        .continue-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px;
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(255,255,255,.9), rgba(244, 235, 220, .92));
            border: 1px solid rgba(109, 78, 45, .14);
            color: #3a2614;
            text-decoration: none;
            box-shadow: 0 10px 18px rgba(50, 34, 17, .08);
            transition: transform 140ms ease, box-shadow 140ms ease;
        }

        .continue-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 22px rgba(50, 34, 17, .12);
            text-decoration: none;
        }

        .continue-link strong {
            display: block;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.02rem;
        }

        .continue-link span {
            color: #6d5234;
            font-size: .86rem;
        }

        .favorite-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .favorite-card {
            display: grid;
            gap: 6px;
            align-content: start;
            padding: 12px;
            border-radius: 14px;
            border: 1px solid rgba(109, 78, 45, .12);
            background: linear-gradient(180deg, rgba(255,255,255,.78), rgba(246, 239, 225, .92));
            color: inherit;
            text-decoration: none;
            box-shadow: 0 10px 18px rgba(50, 34, 17, .06);
            transition: transform 140ms ease, box-shadow 140ms ease;
        }

        .favorite-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 22px rgba(50, 34, 17, .1);
            text-decoration: none;
        }

        .favorite-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-grid;
            place-items: center;
            background: linear-gradient(180deg, #f2c76e, #d99d2d);
            color: white;
            font-size: .86rem;
            box-shadow: 0 8px 14px rgba(201, 143, 39, .22);
        }

        .favorite-label {
            font-family: "Cinzel", "Times New Roman", serif;
            color: #3a2614;
            font-size: .94rem;
        }

        .favorite-count {
            color: #6d5234;
            font-size: .78rem;
        }

        .progress-summary {
            align-items: center;
            justify-content: flex-start;
            gap: 14px;
            margin-bottom: 12px;
            padding-left: 0;
        }

        .progress-overall {
            min-width: 86px;
            padding: 12px 14px;
            border-radius: 16px;
            background: linear-gradient(180deg, rgba(242, 199, 110, .22), rgba(157, 201, 183, .18));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.65);
        }

        .progress-overall small {
            display: block;
            margin-top: 4px;
        }

        .progress-stack {
            padding-left: 0;
        }

        .progress-row-head {
            color: #4a3117;
        }

        .progress-bar {
            background: rgba(255,255,255,.66);
        }

        .completion-item {
            padding: 10px 0;
        }

        .completion-title {
            align-items: baseline;
        }

        .completion-missing {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 2px;
        }

        .completion-missing span {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(255,255,255,.68);
            border: 1px solid rgba(104, 73, 40, .12);
        }

        .activity-item {
            padding: 12px 14px;
            margin-left: 0;
            border-top: 1px solid rgba(102, 73, 41, .12);
            border-radius: 14px;
            background: rgba(255,255,255,.42);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.45);
        }

        .activity-head {
            align-items: flex-start;
        }

        .activity-title {
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1rem;
        }

        .birthday-blast {
            border-radius: 20px;
            border-color: rgba(113, 74, 34, .26);
            box-shadow: 0 18px 30px rgba(99, 62, 24, .18);
        }

        @media (max-width: 980px) {
            .dashboard-hero-top,
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-grid .span-8,
            .dashboard-grid .span-4 {
                grid-column: span 12;
            }

            .favorite-grid {
                grid-template-columns: 1fr;
            }

            .continue-link {
                align-items: flex-start;
                flex-direction: column;
            }
        }
        :root { --paper:#f1e6cc; --ink:#2b1d14; --accent:#6b1e2a; --leather:#1b1511; --gold:#a8842f; }
        .journal { min-height:100%; gap:16px; }
        .dashboard-hero,.dashboard-card,.sheet,.journal-cover { border:1px solid rgba(92,65,39,.55); border-radius:4px; background-color:var(--paper); background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E"); box-shadow:2px 3px 0 rgba(60,40,20,.22); }
        .dashboard-card::before,.dashboard-card::after { display:none; }
        .dashboard-card { outline:1px solid rgba(92,65,39,.38); outline-offset:3px; margin:3px; }
        .dashboard-hero { padding:18px 20px; }
        .journal-title { font-family:"Cinzel",serif; color:#34251b; letter-spacing:.06em; }
        .journal-title::after { content:""; display:block; width:min(280px,55%); height:5px; margin-top:8px; background:linear-gradient(180deg,transparent 1px,var(--gold) 1px 2px,transparent 2px 4px,var(--gold) 4px 5px); }
        .journal-meta,.dashboard-card-title,.stat-label,.sticky-label { font-family:"Cinzel",serif; font-variant:small-caps; letter-spacing:.13em; }
        .journal-meta { border-radius:2px; border:1px solid var(--gold); background:transparent; color:#493624; }
        .dashboard-card-title { color:#39271f; }
        .dashboard-grid { grid-template-columns:minmax(0,1fr); gap:16px; }
        .dashboard-grid .span-8,.dashboard-grid .span-6,.dashboard-grid .span-4 { grid-column:span 12; }
        .action-pill { border:0; border-radius:2px; padding:6px 12px 6px 7px; background:transparent; box-shadow:none; color:#3c2b21; font-family:"Cinzel",serif; font-size:.82rem; font-variant:small-caps; letter-spacing:.08em; transition:transform 180ms ease,box-shadow 180ms ease; }
        .action-pill:hover { transform:translateY(-2px); background:rgba(107,30,42,.06); box-shadow:0 1px 0 rgba(60,40,20,.12); }
        .action-icon { width:32px; height:32px; border:2px solid #9f6d5e; outline:1px solid var(--accent); outline-offset:2px; background:radial-gradient(circle at 35% 30%,#963d49,var(--accent) 70%); color:#f7e6d0; box-shadow:inset 1px 2px 3px rgba(20,5,7,.45),2px 2px 0 rgba(60,40,20,.25); border-radius:50%; }
        .activity-item { grid-template-columns:125px minmax(0,1fr); column-gap:14px; padding:10px 0; border-top:1px dashed rgba(102,73,41,.3); }
        .activity-head { grid-column:1; grid-row:1 / span 3; display:block; }
        .activity-action { padding:0; border:0; border-radius:0; background:none; color:var(--accent); font-family:"Cinzel",serif; font-size:.72rem; letter-spacing:.08em; }
        .activity-meta { color:#554333; }
        .activity-head .activity-meta { display:block; margin-top:4px; font-family:"Segoe Print","Comic Sans MS",cursive; color:#6b3d37; }
        .activity-title { grid-column:2; font-family:"Cormorant Garamond","EB Garamond",serif; font-size:1.1rem; color:#30251d; }
        .activity-item > .activity-meta { grid-column:2; }
        .dashboard-note { color:#554333; }
        .dashboard-card,.dashboard-hero,.action-pill,.btn-mini { transition:transform 180ms ease,box-shadow 180ms ease; }
        .dashboard-card:hover { transform:translateY(-1px); box-shadow:1px 2px 0 rgba(60,40,20,.18); }
        .empty-journal { display:flex; align-items:center; gap:14px; padding:12px 0; }
        .empty-journal svg { width:76px; height:76px; flex:0 0 auto; }
        .empty-journal p { margin:0; font-family:"Caveat",cursive; color:#60433a; font-size:1.05rem; }
        @media(max-width:700px) { .activity-item { grid-template-columns:1fr; gap:3px; } .activity-head { grid-column:1; grid-row:auto; display:flex; justify-content:space-between; } .activity-title,.activity-item > .activity-meta { grid-column:1; } }
        /* Match the supplied folio reference while keeping the canvas fluid. */
        .action-icon svg { width:24px; height:24px; fill:none; stroke:#f2dfad; stroke-width:1.65; stroke-linecap:round; stroke-linejoin:round; }
        @media(min-width:981px) {
            .wrap { width:min(1400px,97vw); min-height:min(668px,calc(100vh - 52px)); margin:26px auto; border:1px solid rgba(191,158,105,.55); border-radius:5px; background:#211612; box-shadow:0 7px 24px rgba(0,0,0,.35); }
            .chrome { min-height:min(668px,calc(100vh - 52px)); grid-template-columns:260px minmax(0,1fr); }
            .chrome.is-collapsed { grid-template-columns:0 minmax(0,1fr); }
            .sidebar { border-right:1px solid rgba(139,37,49,.6); padding:22px 14px 20px; }
            .brand { min-height:92px; display:flex; align-items:flex-start; justify-content:center; border-bottom:1px solid rgba(170,129,62,.5); margin:0 0 24px; padding:8px 4px 16px; }
            .brand-wordmark { gap:0; font-size:1.42rem; letter-spacing:.17em; }
            .brand-wordmark i { display:none; }
            .brand { position:relative; }
            .brand::after { content:"—  ✦  —"; position:absolute; left:0; right:0; bottom:1px; text-align:center; color:#ae873d; font:600 .82rem/1 "Cinzel",serif; letter-spacing:.1em; }
            .nav-link,.back-portals-link { padding:8px 10px 8px 14px; min-height:38px; font-size:.9rem; letter-spacing:.12em; text-transform:uppercase; }
            .nav-link:hover { transform:translateX(1px); background:rgba(255,255,255,.24); box-shadow:none; }
            .sidebar-bottom { margin-top:auto; padding-top:14px; }
            header { min-height:76px; padding:14px 30px; }
            .content { padding:34px 42px 40px 92px; }
            .content::before { left:46px; }
            .content::after { left:14px; width:22px; background-size:22px 46px; }
            .journal { gap:24px; }
            .dashboard-hero { min-height:300px; padding:32px 38px; }
            .journal-meta { margin:0; border:0; background:none; box-shadow:none; padding:0; color:#7c3b3b; }
            .journal-title { margin-top:10px; font-size:clamp(2.2rem,3.3vw,3rem); }
            .journal-title::after { width:212px; margin-top:22px; }
            .dashboard-actions { display:flex; flex-wrap:nowrap; margin-top:28px; gap:34px; }
            .action-pill { display:grid; justify-items:center; gap:9px; min-width:102px; padding:7px 8px; }
            .action-icon { width:64px; height:64px; font-size:1rem; }
            .action-icon svg { width:26px; height:26px; stroke-width:1.35; }
            .dashboard-card--activity { display:flex; align-items:center; min-height:180px; padding:24px 34px; }
            .dashboard-card--activity .dashboard-card-head { position:static; display:block; order:2; flex:1; margin:0; padding:0; }
            .dashboard-card--activity .dashboard-card-head::before { display:none; }
            .dashboard-card--activity .dashboard-card-head::after { display:none; }
            .dashboard-card--activity .empty-journal { position:static; order:1; width:120px; flex:0 0 120px; padding:0; margin-right:40px; }
            .dashboard-card--activity .empty-journal svg { width:120px; height:120px; }
            .dashboard-card--activity .empty-caption { margin:14px 0 0; font:400 28px/1 "Caveat",cursive; color:#705344; }
            .dashboard-card--activity .dashboard-card-title { margin:0 0 4px; }
            .dashboard-card--activity .dashboard-card-subtitle { margin:0; }
        }
        @media(max-width:980px) { .dashboard-actions { flex-wrap:wrap; gap:18px; } .dashboard-card--activity { display:block; } .dashboard-card--activity .empty-journal { position:static; width:auto; margin:0; } .dashboard-card--activity .empty-caption { margin:0 0 0 90px; font-size:24px; } }
        @media(max-width:600px) { .dashboard-actions { justify-content:flex-start; gap:14px; } }
        /* Requested final values: sidebar, binding and activity composition. */
        :root { --paper:#f1e6cc; --ink:#2b1d14; --accent:#6b1e2a; --gold:#a8842f; --leather:#1b1511; }
        body::before,body::after { display:none!important; }
        .nav-link,.back-portals-link { width:100%; min-height:0; margin:0; padding:15px 28px; border:0; border-radius:0; color:#2b1d14; font:400 13px/1.25 "Cinzel",serif; letter-spacing:.16em; text-align:left; text-transform:uppercase; transition:padding-left 180ms ease,color 180ms ease,background 180ms ease; }
        .nav-link { display:flex; flex-direction:row; align-items:center; justify-content:flex-start; }
        .sidebar > .nav-group > .nav-link,
        .sidebar > .nav-accordion > summary.nav-link { margin-bottom:7px; }
        .sidebar > .nav-group { margin-bottom:7px; }
        .nav-link:hover,.back-portals-link:hover { padding-left:34px; color:#6b1e2a; background:transparent; box-shadow:none; transform:none; }
        .nav-link.active { position:relative; padding-left:28px; color:#6b1e2a; font-weight:700; background:linear-gradient(90deg,rgba(107,30,42,.1),transparent); box-shadow:none; }
        .nav-link.active::before { content:""; position:absolute; left:0; top:50%; width:14px; height:44px; transform:translateY(-50%); background:#6b1e2a; clip-path:polygon(0 0,100% 0,100% 100%,50% 78%,0 100%); }
        .nav-link.active .nav-name { font-weight:700; text-decoration:none; }
        .nav-accordion-body { padding:0; }
        .sidebar-bottom { margin-top:auto; padding-top:12px; }
        .sidebar-bottom form button.back-portals-link { font:400 13px/1.25 "Cinzel",serif; letter-spacing:.16em; text-transform:uppercase; }
        header { background:linear-gradient(110deg,#2a1f18,#1f1712); border-bottom:2px solid #a8842f; }
        .sidebar-toggle { width:38px; height:38px; border:1px solid #a8842f; border-radius:2px; background:transparent; }
        .content::before { width:2px; background:#b4535b; }
        .content::after { width:22px; background:radial-gradient(circle at 50% 7px,#17110e 0 5px,#a8842f 5.5px 7px,transparent 7.5px); background-size:22px 46px; background-repeat:repeat-y; opacity:1; }
        .empty-caption { color:#705344; }
        @media(max-width:980px) { .nav-link,.back-portals-link { padding:11px 18px; } .nav-link:hover { padding-left:24px; } }
    </style>

    <div class="journal">
        @php
            $quickActions = [
                ['label' => 'Personnage', 'icon' => 'person', 'url' => route('manage.characters.create')],
                ['label' => 'Événement', 'icon' => 'event', 'url' => route('manage.chronicles.create')],
                ['label' => 'Lieu', 'icon' => 'place', 'url' => route('manage.places.create')],
                ['label' => 'Chronique', 'icon' => 'chronicle', 'url' => route('manage.chronicles.index')],
            ];

            $searchResultCount = collect($searchResults)->sum(fn ($group) => count($group['items'] ?? []));
            $worldSummary = $activeWorld && trim((string) $activeWorld->summary) !== ''
                ? trim(strip_tags($activeWorld->summary))
                : null;
        @endphp

        @if($todayBirthdays->isNotEmpty())
            <section class="birthday-blast">
                <div class="birthday-stage" aria-hidden="true">
                    <span class="birthday-ribbon left"></span>
                    <span class="birthday-ribbon right"></span>
                    <span class="birthday-balloon one"></span>
                    <span class="birthday-balloon two"></span>
                    <span class="birthday-balloon three"></span>
                    <span class="birthday-sparkle a"></span>
                    <span class="birthday-sparkle b"></span>
                    <span class="birthday-sparkle c"></span>
                    <span class="birthday-sparkle d"></span>
                </div>
                <canvas id="birthday-confetti" class="confetti-canvas" aria-hidden="true"></canvas>
                <div class="birthday-head">
                    <div class="birthday-head-left">
                        <h2 class="birthday-title">Joyeux anniversaire !</h2>
                    </div>
                    <div class="birthday-head-actions">
                        <button type="button" class="birthday-music-btn" id="birthday-music-toggle">Couper la musique</button>
                        <div class="birthday-cake" aria-hidden="true"></div>
                    </div>
                </div>
                <div class="birthday-list">
                    @foreach($todayBirthdays as $character)
                        @php
                            $initial = strtoupper(mb_substr((string) $character->display_name, 0, 1));
                        @endphp
                        <a class="birthday-card" href="{{ route('manage.characters.show', $character) }}">
                            <div class="birthday-photo-wrap">
                                @if($character->image_path)
                                    <img class="birthday-photo" src="{{ route('media.show', ['path' => $character->image_path]) }}" alt="Photo de {{ $character->display_name }}">
                                @else
                                    <div class="birthday-fallback">{{ $initial ?: '?' }}</div>
                                @endif
                                <span class="party-hat" aria-hidden="true"></span>
                            </div>
                            <div>
                                <div class="birthday-name">{{ $character->display_name }}</div>
                                <div class="birthday-world">{{ optional($character->world)->name ?: 'Monde inconnu' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
            <iframe
                id="birthday-music-frame"
                width="0"
                height="0"
                style="position:absolute; left:-9999px; border:0;"
                allow="autoplay; encrypted-media"
                src="https://www.youtube.com/embed/FhNGLVJKsBg?autoplay=1&loop=1&playlist=FhNGLVJKsBg&controls=0&disablekb=1&modestbranding=1&rel=0"
                title="Musique anniversaire"
            ></iframe>
        @endif

        <section class="dashboard-card dashboard-hero">
            <div class="dashboard-hero-top">
                <div class="dashboard-world">
                    <div class="journal-meta">Carnet du jour</div>
                    <h2 class="journal-title">Carnet de bord</h2>

                    <div class="dashboard-actions">
                        @foreach($quickActions as $action)
                            <a class="action-pill" href="{{ $action['url'] }}">
                                <span class="action-icon" aria-hidden="true">
                                    @if($action['icon'] === 'person')
                                        <svg viewBox="0 0 24 24"><circle cx="12" cy="7" r="3.2"/><path d="M5.5 20c.5-4 2.7-6.1 6.5-6.1s6 2.1 6.5 6.1"/></svg>
                                    @elseif($action['icon'] === 'event')
                                        <svg viewBox="0 0 24 24"><path d="M9 3h6M9 21h6M8 4c0 4 8 5 8 8s-8 4-8 8M16 4c0 4-8 5-8 8s8 4 8 8"/></svg>
                                    @elseif($action['icon'] === 'place')
                                        <svg viewBox="0 0 24 24"><path d="M19 10c0 5-7 12-7 12S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24"><path d="M20 4c-8 0-13 3-13 9 0 2 1.5 3.5 3.5 3.5C16.5 16.5 20 10 20 4Z"/><path d="M4 21c2-5 6-9 12-13"/></svg>
                                    @endif
                                </span>
                                <span>{{ $action['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        @if($searchTerm !== '')
            <section class="dashboard-card search-section">
                <div class="dashboard-card-head">
                    <div>
                        <h3 class="dashboard-card-title">Résultats de recherche</h3>
                        <p class="dashboard-card-subtitle">{{ $searchResultCount }} résultat(s) pour "{{ $searchTerm }}"</p>
                    </div>
                    <a class="btn-mini secondary" href="{{ route('manage.index') }}">Effacer</a>
                </div>

                @if($searchResultCount === 0)
                    <p class="dashboard-note">Aucun résultat trouvé.</p>
                @else
                    <div class="search-results">
                        @foreach($searchResults as $group)
                            @if(!empty($group['items']))
                                <div class="search-group">
                                    <h4>{{ $group['label'] }}</h4>
                                    @foreach($group['items'] as $item)
                                        <div class="search-result-item">
                                            <div class="search-result-main">
                                                <div class="search-result-title">{{ $item['label'] }}</div>
                                                @if(!empty($item['meta']))
                                                    <div class="search-result-meta">{{ $item['meta'] }}</div>
                                                @endif
                                            </div>
                                            <a class="btn-mini search-result-link" href="{{ $item['url'] }}">Ouvrir</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        <section class="dashboard-grid">
            <article class="dashboard-card dashboard-card--activity span-8">
                <div class="dashboard-card-head">
                    <div>
                        <h3 class="dashboard-card-title">Activité récente</h3>
                        <p class="dashboard-card-subtitle">Créations, modifications et suppressions</p>
                        @if($recentActivity->isEmpty())
                            <p class="empty-caption">Les pages sont encore blanches…</p>
                        @endif
                    </div>
                </div>
                @forelse($recentActivity as $item)
                    <div class="activity-item">
                        <div class="activity-head">
                            <span class="activity-action">{{ $item['action'] }}</span>
                            <span class="activity-meta">{{ optional($item['at'])->locale('fr')->translatedFormat('d/m H:i') }}</span>
                        </div>
                        @if(!empty($item['url']))
                            <a class="activity-title" href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                        @else
                            <div class="activity-title">{{ $item['label'] }}</div>
                        @endif
                        <div class="activity-meta">
                            {{ $item['type'] }}@if(!empty($item['meta'])) · {{ $item['meta'] }}@endif
                        </div>
                    </div>
                @empty
                    <div class="empty-journal">
                        <svg viewBox="0 0 120 120" role="img" aria-label="Plume et encrier">
                            <path d="M72 13C49 16 31 32 30 54c0 8 4 13 11 14 19 1 31-21 31-55Z" fill="#f7edda" stroke="#2b1d14" stroke-width="1.7"/><path d="M39 68C54 51 67 34 81 19M49 57c-3-11-1-20 4-29M58 46c7-1 13-5 17-11M39 68l-7 15" fill="none" stroke="#2b1d14" stroke-width="1.5"/><path d="M18 83h54v27H18z" fill="#f3e7d0" stroke="#6b1e2a" stroke-width="1.8"/><path d="M22 83c3-7 43-7 47 0M29 79c4-4 27-4 32 0" fill="none" stroke="#a8842f" stroke-width="1.5"/><path d="M18 89h54M23 110h44" fill="none" stroke="#6b1e2a" stroke-width="1"/><path d="M45 78c1-3 3-5 5-7" fill="none" stroke="#2b1d14" stroke-width="1.4"/>
                        </svg>
                    </div>
                @endforelse
            </article>
        </section>
    </div>

    @if($todayBirthdays->isNotEmpty())
        <script>
            (function () {
                const canvas = document.getElementById('birthday-confetti');
                if (!canvas) return;
                const host = canvas.closest('.birthday-blast');
                if (!host) return;
                const ctx = canvas.getContext('2d');
                const pieces = [];
                const colors = ['#E63946', '#F1C453', '#2A9D8F', '#4F6DCC', '#F28482', '#8B5CF6'];

                function resize() {
                    canvas.width = host.clientWidth;
                    canvas.height = host.clientHeight;
                }

                function spawn(count) {
                    for (let i = 0; i < count; i++) {
                        pieces.push({
                            x: Math.random() * canvas.width,
                            y: Math.random() * canvas.height - canvas.height,
                            w: 4 + Math.random() * 7,
                            h: 7 + Math.random() * 9,
                            c: colors[Math.floor(Math.random() * colors.length)],
                            a: 0.35 + Math.random() * 0.45,
                            vx: -0.9 + Math.random() * 1.8,
                            vy: 1.4 + Math.random() * 2.6,
                            rot: Math.random() * Math.PI,
                            vr: -0.07 + Math.random() * 0.14,
                        });
                    }
                }

                function tick() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    for (let i = 0; i < pieces.length; i++) {
                        const p = pieces[i];
                        p.x += p.vx;
                        p.y += p.vy;
                        p.rot += p.vr;
                        if (p.y > canvas.height + 20) {
                            p.y = -20;
                            p.x = Math.random() * canvas.width;
                        }
                        ctx.save();
                        ctx.translate(p.x, p.y);
                        ctx.rotate(p.rot);
                        ctx.globalAlpha = p.a;
                        ctx.fillStyle = p.c;
                        ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                        ctx.restore();
                    }
                    requestAnimationFrame(tick);
                }

                resize();
                spawn(88);
                tick();
                window.addEventListener('resize', resize);
            })();
        </script>
        <script>
            (function () {
                const frame = document.getElementById('birthday-music-frame');
                const toggle = document.getElementById('birthday-music-toggle');
                if (!frame || !toggle) return;

                const musicUrl = 'https://www.youtube.com/embed/FhNGLVJKsBg?autoplay=1&loop=1&playlist=FhNGLVJKsBg&controls=0&disablekb=1&modestbranding=1&rel=0';
                let enabled = true;

                toggle.addEventListener('click', function () {
                    enabled = !enabled;
                    if (enabled) {
                        frame.src = musicUrl;
                        toggle.textContent = 'Couper la musique';
                    } else {
                        frame.src = 'about:blank';
                        toggle.textContent = 'Remettre la musique';
                    }
                });
            })();
        </script>
    @endif
@endsection
