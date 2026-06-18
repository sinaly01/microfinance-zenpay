package com.microfinance.config;

import com.microfinance.model.Client;
import com.microfinance.model.Compte;
import com.microfinance.model.OffreAbonnement;
import com.microfinance.model.Retrait;
import com.microfinance.model.Versement;
import com.microfinance.model.Virement;
import com.microfinance.model.enums.RoleUtilisateur;
import com.microfinance.model.enums.StatutCompte;
import com.microfinance.model.enums.StatutKyc;
import com.microfinance.model.enums.StatutTransaction;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.CompteRepository;
import com.microfinance.repository.OffreAbonnementRepository;
import com.microfinance.repository.TransactionRepository;
import java.math.BigDecimal;
import java.math.RoundingMode;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;
import java.util.Random;
import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.boot.CommandLineRunner;
import org.springframework.core.annotation.Order;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Component;
import org.springframework.transaction.annotation.Transactional;

@Component
@Order(2)
public class DataGenerator implements CommandLineRunner {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(DataGenerator.class);
   private final ClientRepository clientRepository;
   private final CompteRepository compteRepository;
   private final TransactionRepository transactionRepository;
   private final PasswordEncoder passwordEncoder;
   private final OffreAbonnementRepository offreRepository;
   private static final Random RNG = new Random(42L);
   private static final String[] NOMS = new String[]{"Diallo", "Traoré", "Koné", "Coulibaly", "Touré", "Sylla", "Bah", "Barry", "Camara", "Sow", "Keita", "Doumbia", "Sanogo", "Kouyaté", "Sidibé", "Ouédraogo", "Sawadogo", "Compaoré", "Zongo", "Kaboré", "Tapsoba", "Gnagne", "Kouassi", "Yao", "Kofi", "Mensah", "Asante", "Boateng", "Dembélé", "Sissoko", "Maïga", "Cissé", "Ndiaye", "Fall", "Diop", "Mbaye", "Thiam", "Sène", "Gueye", "Wade", "Fofana", "Balde", "Sané"};
   private static final String[] PRENOMS_H = new String[]{"Mamadou", "Ibrahim", "Moussa", "Oumar", "Amadou", "Seydou", "Boubacar", "Abdoulaye", "Issouf", "Hamidou", "Drissa", "Souleymane", "Adama", "Modibo", "Lamine", "Cheikh", "Aliou", "Pape", "Ousmane", "Alassane", "Kofi", "Kwame", "Kweku", "Yaw", "Kojo", "Fiifi", "Ama", "Abena", "Akwasi", "Mensah"};
   private static final String[] PRENOMS_F = new String[]{"Fatoumata", "Aminata", "Aissatou", "Mariam", "Kadiatou", "Oumou", "Bintou", "Hawa", "Ramata", "Safi", "Rokia", "Nana", "Awa", "Coumba", "Maimouna", "Salimata", "Djeneba", "Korotoumou", "Tenin", "Saran", "Adjoa", "Abena", "Akosua", "Adwoa", "Efua", "Ama", "Akua", "Yaa"};
   private static final String[] VILLES = new String[]{"Abidjan", "Dakar", "Bamako", "Ouagadougou", "Conakry", "Lomé", "Cotonou", "Niamey", "Banjul", "Bissau", "Freetown", "Accra"};
   private static final String[] QUARTIERS = new String[]{"Cocody", "Yopougon", "Abobo", "Koumassi", "Marcory", "Treichville", "Adjamé", "Attécoubé", "Plateau", "Port-Bouet", "Songon", "Bingerville"};
   private static final String[] OPERATEURS = new String[]{"WAVE", "ORANGE", "MTN", "MOOV"};

   @Transactional
   public void run(String... args) {
      if (this.clientRepository.count() > 5L) {
         log.info("Données fictives déjà présentes ({} clients), génération ignorée.", this.clientRepository.count());
      } else {
         log.info("=== Génération des données fictives ===");
         List<Client> clients = this.genererClients(150);
         List<Compte> comptes = this.genererComptes(clients);
         this.genererTransactions(comptes, 600);
         log.info("=== Données générées : {} clients, {} comptes, 600 opérations ===", clients.size(), comptes.size());
      }
   }

   private List genererClients(int nb) {
      Optional<OffreAbonnement> standard = this.offreRepository.findByNomOffre("STANDARD");
      List<OffreAbonnement> offres = this.offreRepository.findAll();
      List<Client> liste = new ArrayList();

      for(int i = 0; i < nb; ++i) {
         boolean femme = RNG.nextBoolean();
         String nom = NOMS[RNG.nextInt(NOMS.length)];
         String prenom = femme ? PRENOMS_F[RNG.nextInt(PRENOMS_F.length)] : PRENOMS_H[RNG.nextInt(PRENOMS_H.length)];
         String ville = VILLES[RNG.nextInt(VILLES.length)];
         String quartier = QUARTIERS[RNG.nextInt(QUARTIERS.length)];
         OffreAbonnement offre = offres.isEmpty() ? null : (OffreAbonnement)offres.get(RNG.nextInt(Math.min(2, offres.size())));
         Client.ClientBuilder var10000 = Client.builder().nom(nom).prenom(prenom).telephone(this.genererTelephone());
         String var10001 = prenom.toLowerCase().replaceAll("[^a-z]", "");
         var10000 = var10000.email(var10001 + "." + nom.toLowerCase().replaceAll("[^a-z]", "") + i + "@email.com").adresse(quartier + ", " + ville);
         Object[] var10002 = new Object[]{i + 1000};
         Client c = var10000.numeroCni("CNI" + String.format("%08d", var10002)).dateNaissance(LocalDate.of(1960 + RNG.nextInt(45), 1 + RNG.nextInt(12), 1 + RNG.nextInt(28))).motDePasse(this.passwordEncoder.encode("Client@2024")).role(RoleUtilisateur.ROLE_CLIENT).statutKyc(StatutKyc.VALIDE).offreAbonnement(offre).operateurMomo(OPERATEURS[RNG.nextInt(OPERATEURS.length)]).actif(true).build();
         liste.add((Client)this.clientRepository.save(c));
      }

      log.info("  ✓ {} clients créés", liste.size());
      return liste;
   }

   private List genererComptes(List clients) {
      List<Compte> liste = new ArrayList();

      for(Client client : clients) {
         int nbComptes = RNG.nextInt(3) == 0 ? 2 : 1;

         for(int j = 0; j < nbComptes; ++j) {
            BigDecimal soldeInit = BigDecimal.valueOf((long)(10000 + RNG.nextInt(490000))).setScale(2, RoundingMode.HALF_UP);
            Compte compte = Compte.builder().client(client).solde(soldeInit).statut(this.choisirStatut()).plafondRetrait(BigDecimal.valueOf((long)(200000 + RNG.nextInt(300000)))).montantMinSolde(BigDecimal.valueOf(5000L)).tauxAgios(new BigDecimal("0.0050")).build();
            liste.add((Compte)this.compteRepository.save(compte));
         }
      }

      log.info("  ✓ {} comptes créés", liste.size());
      return liste;
   }

   private void genererTransactions(List comptes, int nb) {
      List<Compte> actifs = comptes.stream().filter(Compte::isActif).toList();
      if (actifs.isEmpty()) {
         log.warn("Aucun compte actif, transactions ignorées.");
      } else {
         int versements = 0;
         int retraits = 0;
         int virements = 0;

         for(int i = 0; i < nb; ++i) {
            Compte source = (Compte)actifs.get(RNG.nextInt(actifs.size()));
            int type = RNG.nextInt(10);
            LocalDateTime date = LocalDateTime.now().minusDays((long)RNG.nextInt(365)).minusHours((long)RNG.nextInt(24));
            if (type <= 4) {
               BigDecimal montant = this.montantAleatoire(5000, 200000);
               Versement v = new Versement();
               v.setCompte(source);
               v.setMontant(montant);
               v.setSource("DEPOT_ESPECES");
               v.setDescription("Versement client");
               v.setStatut(StatutTransaction.VALIDEE);
               v.setDateHeure(date);
               v.setReference("VRS" + System.nanoTime());
               source.setSolde(source.getSolde().add(montant));
               this.compteRepository.save(source);
               this.transactionRepository.save(v);
               ++versements;
            } else if (type <= 7) {
               BigDecimal montant = this.montantAleatoire(5000, 100000);
               if (source.getSolde().subtract(montant).compareTo(source.getMontantMinSolde()) >= 0 && montant.compareTo(source.getPlafondRetrait()) <= 0) {
                  Retrait r = new Retrait();
                  r.setCompte(source);
                  r.setMontant(montant);
                  r.setCanal((new String[]{"GUICHET", "ATM", "MOBILE"})[RNG.nextInt(3)]);
                  r.setDescription("Retrait client");
                  r.setStatut(StatutTransaction.VALIDEE);
                  r.setDateHeure(date);
                  r.setReference("RTR" + System.nanoTime());
                  source.setSolde(source.getSolde().subtract(montant));
                  this.compteRepository.save(source);
                  this.transactionRepository.save(r);
                  ++retraits;
               }
            } else if (actifs.size() >= 2) {
               Compte dest = (Compte)actifs.get(RNG.nextInt(actifs.size()));
               if (!dest.getIdCompte().equals(source.getIdCompte())) {
                  BigDecimal montant = this.montantAleatoire(5000, 150000);
                  if (source.getSolde().subtract(montant).compareTo(source.getMontantMinSolde()) >= 0) {
                     Virement vir = new Virement();
                     vir.setCompte(source);
                     vir.setCompteDestination(dest);
                     vir.setMontant(montant);
                     vir.setDescription("Virement entre comptes");
                     vir.setStatut(StatutTransaction.VALIDEE);
                     vir.setDateHeure(date);
                     vir.setReference("VIR" + System.nanoTime());
                     source.setSolde(source.getSolde().subtract(montant));
                     dest.setSolde(dest.getSolde().add(montant));
                     this.compteRepository.save(source);
                     this.compteRepository.save(dest);
                     this.transactionRepository.save(vir);
                     ++virements;
                  }
               }
            }
         }

         log.info("  ✓ Transactions : {} versements, {} retraits, {} virements", new Object[]{versements, retraits, virements});
      }
   }

   private String genererTelephone() {
      String[] prefixes = new String[]{"+221", "+225", "+223", "+226", "+224", "+228"};
      String prefix = prefixes[RNG.nextInt(prefixes.length)];
      return prefix + String.format("%08d", RNG.nextInt(99999999));
   }

   private BigDecimal montantAleatoire(int min, int max) {
      int val = min + RNG.nextInt(max - min);
      val = val / 500 * 500;
      return BigDecimal.valueOf((long)val).setScale(2, RoundingMode.HALF_UP);
   }

   private StatutCompte choisirStatut() {
      int r = RNG.nextInt(10);
      if (r <= 7) {
         return StatutCompte.ACTIF;
      } else {
         return r == 8 ? StatutCompte.SUSPENDU : StatutCompte.BLOQUE;
      }
   }

   @Generated
   public DataGenerator(final ClientRepository clientRepository, final CompteRepository compteRepository, final TransactionRepository transactionRepository, final PasswordEncoder passwordEncoder, final OffreAbonnementRepository offreRepository) {
      this.clientRepository = clientRepository;
      this.compteRepository = compteRepository;
      this.transactionRepository = transactionRepository;
      this.passwordEncoder = passwordEncoder;
      this.offreRepository = offreRepository;
   }
}
