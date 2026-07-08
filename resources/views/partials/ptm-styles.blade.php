<style>
    :root {
        --c-primary: #3b7ddd;
        --c-success: #1cbb8c;
        --c-info: #17a2b8;
        --c-warning: #f0ad4e;
        --c-danger: #dc3545;
        --c-secondary: #8a93a3;
        --c-pink: #e85d9e;
        --c-teal: #0fb5ae;
        --c-amber: #f1a93d;
        --c-ink: #1f2937;
        --c-muted: #8a93a3;
        --c-border: #eef1f6;
        --radius-premium: 16px;
    }

    body { background-color: #f5f7fb; }

    .panel-card {
        border-radius: var(--radius-premium) !important;
        box-shadow: 0 4px 20px rgba(20, 30, 60, 0.03) !important;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.01) !important;
    }

    .stat-card {
        position: relative;
        border-radius: var(--radius-premium);
        box-shadow: 0 2px 10px rgba(20, 30, 60, 0.04);
        transition: transform .2s ease, box-shadow .2s ease;
        background: #ffffff;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(20, 30, 60, 0.08); }
    .stat-label { color: var(--c-muted); font-size: .8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
    .stat-number { color: var(--c-ink); font-weight: 700; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .stat-icon-primary { background: rgba(59,125,221,.12); color: var(--c-primary); }
    .stat-icon-success { background: rgba(28,187,140,.12); color: var(--c-success); }
    .stat-icon-info { background: rgba(23,162,184,.12); color: var(--c-info); }
    .stat-icon-warning { background: rgba(240,173,78,.12); color: var(--c-warning); }
    .stat-icon-danger { background: rgba(220,53,69,.12); color: var(--c-danger); }
    .stat-icon-teal { background: rgba(15,181,174,.12); color: var(--c-teal); }

    .panel-icon { width: 34px; height: 34px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: .85rem; }
    .panel-icon-amber { background: rgba(241,169,61,.15); color: var(--c-amber); }
    .panel-icon-teal { background: rgba(15,181,174,.15); color: var(--c-teal); }
    .panel-icon-pink { background: rgba(232,93,158,.15); color: var(--c-pink); }
    .panel-icon-primary { background: rgba(59,125,221,.15); color: var(--c-primary); }

    .fs-2xs { font-size: 0.68rem; }
    .fs-3xs { font-size: 0.62rem; }
    .fs-xs { font-size: 0.75rem; }
    .tracking-wider { letter-spacing: 0.06em; }
    .text-teal { color: var(--c-teal) !important; }
    .bg-soft-teal { background-color: rgba(15, 181, 174, 0.1) !important; }
    .bg-soft-primary { background-color: rgba(59, 125, 221, 0.1); }
    .text-primary { color: var(--c-primary) !important; }
    .bg-soft-success { background-color: rgba(28, 187, 140, 0.1); }
    .text-success { color: var(--c-success) !important; }
    .bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
    .text-info { color: var(--c-info) !important; }
    .bg-soft-warning { background-color: rgba(240, 173, 78, 0.1); }
    .text-warning { color: var(--c-warning) !important; }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
    .text-danger { color: var(--c-danger) !important; }
    .bg-soft-secondary { background-color: rgba(138, 147, 163, 0.12); }
    .text-secondary { color: var(--c-secondary) !important; }

    .empty-state { text-align: center; padding: 40px 10px; color: var(--c-muted); }
    .empty-state i { font-size: 1.6rem; margin-bottom: 8px; display: block; opacity: .5; }
    .empty-state p { font-size: .85rem; }

    .modern-table thead th { font-size: .72rem; text-transform: uppercase; letter-spacing: .03em; color: var(--c-muted); font-weight: 700; border-bottom: 1px solid var(--c-border); }
    .modern-table tbody tr { border-bottom: 1px solid var(--c-border); }
    .modern-table tbody tr:last-child { border-bottom: none; }

    /* Progress bars */
    .ptm-progress { height: 8px; border-radius: 999px; background: #eef1f6; overflow: hidden; }
    .ptm-progress-bar { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--c-primary), var(--c-teal)); transition: width .4s ease; }
    .ptm-progress-bar.is-complete { background: linear-gradient(90deg, var(--c-success), var(--c-teal)); }
    .ptm-progress-bar.is-danger { background: linear-gradient(90deg, var(--c-danger), var(--c-warning)); }

    /* Project cards (index grid) */
    .project-card { border-radius: 16px; border: 1px solid var(--c-border); padding: 18px; background: #fff; transition: all .2s ease; display: flex; flex-direction: column; height: 100%; text-decoration: none; }
    .project-card:hover { box-shadow: 0 10px 24px rgba(20,30,60,.08); transform: translateY(-3px); border-color: rgba(59,125,221,.25); }
    .project-code { font-family: 'Courier New', monospace; font-size: .68rem; color: var(--c-muted); letter-spacing: .04em; }
    .project-title { color: var(--c-ink); font-weight: 700; font-size: 1rem; margin: 4px 0 8px; }
    .avatar-stack { display: flex; }
    .avatar-stack .avatar-chip { width: 26px; height: 26px; border-radius: 50%; background: var(--c-primary); color: #fff; font-size: .65rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; margin-left: -8px; }
    .avatar-stack .avatar-chip:first-child { margin-left: 0; }

    /* Kanban board */
    .kanban-board { display: grid; grid-template-columns: repeat(4, minmax(240px, 1fr)); gap: 14px; overflow-x: auto; padding-bottom: 6px; }
    .kanban-column { background: #f8fafc; border-radius: 14px; padding: 12px; min-height: 300px; border: 1px solid var(--c-border); }
    .kanban-column-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; padding: 0 4px; }
    .kanban-column-title { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; color: var(--c-ink); }
    .kanban-count { font-size: .68rem; font-weight: 700; background: #e5e9f0; color: var(--c-muted); border-radius: 999px; padding: 2px 8px; }
    .kanban-column.drag-over { background: rgba(59,125,221,.06); border-color: rgba(59,125,221,.3); }
    .kanban-card { background: #fff; border: 1px solid var(--c-border); border-radius: 12px; padding: 12px; margin-bottom: 10px; cursor: grab; transition: box-shadow .2s ease, transform .15s ease; }
    .kanban-card:hover { box-shadow: 0 6px 16px rgba(20,30,60,.07); }
    .kanban-card.dragging { opacity: .5; }
    .kanban-card-title { font-size: .85rem; font-weight: 600; color: var(--c-ink); margin-bottom: 6px; }
    .kanban-priority-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }
    .kanban-card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; }
    .kanban-assignee-chip { width: 24px; height: 24px; border-radius: 50%; background: var(--c-primary); color: #fff; font-size: .62rem; font-weight: 700; display: flex; align-items: center; justify-content: center; }
    .kanban-due { font-size: .68rem; color: var(--c-muted); }
    .kanban-due.is-overdue { color: var(--c-danger); font-weight: 700; }
    .add-task-trigger { border: 1.5px dashed var(--c-border); border-radius: 12px; padding: 10px; text-align: center; color: var(--c-muted); font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .2s; }
    .add-task-trigger:hover { border-color: var(--c-primary); color: var(--c-primary); background: rgba(59,125,221,.04); }

    /* Ticket badges */
    .ticket-number-chip { font-family: 'Courier New', monospace; font-size: .72rem; font-weight: 700; color: var(--c-primary); background: rgba(59,125,221,.08); padding: 3px 8px; border-radius: 8px; }

    /* Activity / comment feed */
    .activity-feed { max-height: 380px; overflow-y: auto; padding-right: 4px; }
    .activity-feed::-webkit-scrollbar, .notification-feed::-webkit-scrollbar, .kanban-board::-webkit-scrollbar { height: 6px; width: 6px; }
    .activity-feed::-webkit-scrollbar-thumb { background: #dfe4ee; border-radius: 10px; }
    .comment-item { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--c-border); }
    .comment-item:last-child { border-bottom: none; }
    .comment-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--c-teal); color: #fff; display: flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 700; flex: 0 0 auto; }

    .member-chip { display: flex; align-items: center; gap: 8px; padding: 6px 10px; border: 1px solid var(--c-border); border-radius: 999px; font-size: .78rem; font-weight: 600; color: var(--c-ink); background: #fff; }
</style>
