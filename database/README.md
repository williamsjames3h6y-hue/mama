# Database Setup Instructions

## Initial Setup

1. Import the base schema:
   ```sql
   mysql -u username -p database_name < database/schema.sql
   ```

## Migrations

### Add VIP Levels (Required for new features)

Run this migration to add VIP level support:

```sql
mysql -u username -p database_name < database/migration_add_vip_levels.sql
```

This migration adds:
- `vip_level` column to users table (default: 1)
- `vip_level_required` column to projects table (default: 1)
- VIP rate settings (vip1_rate through vip5_rate)
- Contact information settings (phone, address)
- Updates site name to EarningsLLC

## VIP Level System

Users start at VIP Level 1 upon registration and can access:
- VIP 1: Projects requiring VIP 1 or lower (default rate: $10)
- VIP 2: Projects requiring VIP 2 or lower (default rate: $20)
- VIP 3: Projects requiring VIP 3 or lower (default rate: $50)
- VIP 4: Projects requiring VIP 4 or lower (default rate: $100)
- VIP 5: Projects requiring VIP 5 or lower (default rate: $200)

Admins can:
- Set VIP rates in Admin Settings
- Create projects with specific VIP level requirements
- View user VIP levels in the Users management page
