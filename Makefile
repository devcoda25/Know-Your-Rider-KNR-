.PHONY: up down logs reset admin mobile

up:            ## Start postgres, redis, gateway, auth and registry
	docker compose up --build

down:
	docker compose down

logs:
	docker compose logs -f gateway auth registry

reset:         ## Wipe database volumes and start clean
	docker compose down -v

admin:         ## Run the Next.js super admin app
	cd apps/admin-web && npm install && npm run dev

mobile:        ## Run the Expo React Native app
	cd apps/mobile && npm install && npx expo start
