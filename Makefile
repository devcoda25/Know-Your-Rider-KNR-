.PHONY: up down logs reset admin mobile

up:            ## Start postgres, redis, gateway, auth and registry
	cd knr-backend && docker compose up --build

down:
	cd knr-backend && docker compose down

logs:
	cd knr-backend && docker compose logs -f gateway auth registry

reset:         ## Wipe database volumes and start clean
	cd knr-backend && docker compose down -v

admin:         ## Run the Next.js admin platform
	cd admin-platform && npm install && npm run dev

mobile:        ## Run the Expo React Native app
	cd mobileuserapp && npm install && npx expo start
