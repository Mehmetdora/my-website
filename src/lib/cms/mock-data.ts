import type { LifePost, Note } from "@/types/content";
import type { Category, Media, Tag } from "@/types/common";
import type { Post } from "@/types/post";
import type { Project } from "@/types/project";

const img = (id: string, seed: string, alt: string): Media => ({
  id,
  url: `https://images.unsplash.com/${seed}?auto=format&fit=crop&w=1400&q=80`,
  alt,
  width: 1400,
  height: 900,
});

export const tags: Tag[] = [
  { id: "t1", name: "STM32", slug: "stm32" },
  { id: "t2", name: "UART", slug: "uart" },
  { id: "t3", name: "DMA", slug: "dma" },
  { id: "t4", name: "ESP32", slug: "esp32" },
  { id: "t5", name: "FreeRTOS", slug: "freertos" },
  { id: "t6", name: "C", slug: "c" },
];

export const categories: Category[] = [
  { id: "c1", name: "Embedded C", slug: "embedded-c" },
  { id: "c2", name: "Mikrodenetleyiciler", slug: "mikrodenetleyiciler" },
  { id: "c3", name: "IoT", slug: "iot" },
];

export const posts: Post[] = [
  {
    id: "p1",
    title: "STM32 UART DMA Receive Mantığını Kurmak",
    slug: "stm32-uart-dma-receive",
    summary: "Interrupt yükünü azaltan, buffer yönetimi daha temiz bir UART DMA yaklaşımı.",
    coverImage: img("m1", "photo-1518770660439-4636190af475", "Elektronik devre kartı"),
    category: categories[0],
    tags: [tags[0], tags[1], tags[2], tags[5]],
    status: "published",
    visibility: "public",
    publishedAt: "2026-05-14",
    updatedAt: "2026-05-18",
    readingTime: 6,
    content: [
      { type: "paragraph", text: "UART DMA, gömülü projelerde CPU üzerindeki gereksiz interrupt yükünü azaltmak için çok kullanışlıdır." },
      { type: "heading", level: 2, text: "Neden DMA?" },
      { type: "paragraph", text: "Sürekli veri akan sistemlerde byte byte interrupt almak yerine çevresel birimin belleğe yazmasına izin vermek daha sakin bir akış sağlar." },
      { type: "callout", tone: "warning", title: "Dikkat", text: "Callback içinde uzun işlem yapmak yerine bayrak set edip işi task veya main loop tarafına bırak." },
      { type: "code", language: "c", filename: "uart_dma.c", code: "HAL_UARTEx_ReceiveToIdle_DMA(&huart2, rxBuffer, RX_BUFFER_SIZE);\n__HAL_DMA_DISABLE_IT(huart2.hdmarx, DMA_IT_HT);" },
      { type: "heading", level: 2, text: "Buffer stratejisi" },
      { type: "list", items: ["Ring buffer kullan", "Idle line event ile paket sınırı yakala", "Parser katmanını UART callback'ten ayır"] },
    ],
  },
  {
    id: "p2",
    title: "FreeRTOS Task Tasarımında İlk Prensipler",
    slug: "freertos-task-tasarimi",
    summary: "Task, queue ve semaphore kararlarını sade tutmak için pratik notlar.",
    coverImage: img("m2", "photo-1558494949-ef010cbdcc31", "Sunucu ve elektronik altyapı"),
    category: categories[1],
    tags: [tags[0], tags[4], tags[5]],
    status: "published",
    visibility: "public",
    publishedAt: "2026-05-09",
    readingTime: 5,
    content: [
      { type: "paragraph", text: "FreeRTOS tasarımında hedef, her işi task yapmak değil, sistemi okunabilir sorumluluklara ayırmaktır." },
      { type: "heading", level: 2, text: "Task sınırları" },
      { type: "quote", text: "Bir task gerçek bir zamanlama ihtiyacını veya bağımsız sorumluluğu temsil etmeli." },
    ],
  },
  {
    id: "p3",
    title: "ESP32 BLE Advertisement Paketlerini Okumak",
    slug: "esp32-ble-advertisement",
    summary: "BLE broadcaster projemde reklam paketlerini parse ederken tuttuğum notlar.",
    coverImage: img("m3", "photo-1550751827-4bd374c3f58b", "Kablosuz ağ ve devre temsili"),
    category: categories[2],
    tags: [tags[3], tags[5]],
    status: "published",
    visibility: "public",
    publishedAt: "2026-04-22",
    readingTime: 4,
    githubUrl: "https://github.com/",
    content: [
      { type: "paragraph", text: "BLE advertisement paketleri küçük görünür ama doğru ayrıştırma için alan uzunluklarını disiplinli takip etmek gerekir." },
      { type: "code", language: "cpp", filename: "packet_parser.cpp", code: "while (index < payloadLength) {\n  const uint8_t fieldLength = payload[index++];\n  const uint8_t fieldType = payload[index++];\n}" },
    ],
  },
];

export const projects: Project[] = [
  {
    id: "pr1",
    title: "ESP32 BLE Broadcaster",
    slug: "esp32-ble-broadcaster",
    shortDescription: "ESP32 ile BLE advertisement yayınlayan ve paket alanlarını ayrıştıran küçük embedded lab projesi.",
    coverImage: img("pm1", "photo-1581092160607-ee22621dd758", "ESP32 ve sensörler"),
    technologies: ["C++", "ESP32", "BLE", "Arduino"],
    hardwareUsed: ["ESP32 DevKit", "USB serial", "Logic analyzer"],
    softwareUsed: ["Arduino IDE", "Serial Monitor", "Git"],
    status: "in-progress",
    difficultyLevel: "intermediate",
    visibility: "public",
    tags: [tags[3], tags[5]],
    githubUrl: "https://github.com/",
    startDate: "2026-04-01",
    longDescription: [
      { type: "paragraph", text: "Bu proje kişisel embedded lab için BLE tarafında temel yayın ve paket okuma akışını anlamaya odaklanıyor." },
      { type: "heading", level: 2, text: "Öğrendiklerim" },
      { type: "list", items: ["Advertisement alan yapısı", "Payload uzunluk kontrolü", "Seri port üzerinden debug akışı"] },
    ],
  },
  {
    id: "pr2",
    title: "STM32 Sensor Dashboard",
    slug: "stm32-sensor-dashboard",
    shortDescription: "STM32 üzerinden sensör okumalarını toplayıp seri arayüzle görselleştiren dashboard altyapısı.",
    coverImage: img("pm2", "photo-1581093588401-fbb62a02f120", "Mikrodenetleyici geliştirme masası"),
    technologies: ["C", "STM32", "UART", "DMA"],
    hardwareUsed: ["STM32F407 Discovery", "HC-SR04", "Breadboard"],
    softwareUsed: ["STM32CubeIDE", "KiCad"],
    status: "planned",
    difficultyLevel: "advanced",
    visibility: "public",
    tags: [tags[0], tags[1], tags[2]],
    startDate: "2026-06-01",
    longDescription: [
      { type: "paragraph", text: "Planlanan bu proje, sensör verisini daha temiz bir veri hattı üzerinden izlemeyi hedefliyor." },
    ],
  },
  {
    id: "pr3",
    title: "FreeRTOS Mini Scheduler Notes",
    slug: "freertos-mini-scheduler-notes",
    shortDescription: "FreeRTOS task ve queue örneklerini deneysel küçük uygulamalarla belgeleyen çalışma.",
    coverImage: img("pm3", "photo-1516321318423-f06f85e504b3", "Kod ve geliştirme ortamı"),
    technologies: ["C", "FreeRTOS", "STM32"],
    hardwareUsed: ["STM32 Nucleo", "LED matrix"],
    softwareUsed: ["STM32CubeIDE"],
    status: "completed",
    difficultyLevel: "beginner",
    visibility: "public",
    tags: [tags[4], tags[0], tags[5]],
    longDescription: [
      { type: "paragraph", text: "Küçük task örnekleriyle RTOS zihniyetini pratikte oturtmak için hazırlanmış bir not serisi." },
    ],
  },
];

export const lifePosts: LifePost[] = [
  {
    id: "l1",
    title: "Lab masasını toparladığım gün",
    slug: "lab-masasini-toparladigim-gun",
    excerpt: "Kablolar, not defterleri ve yarım kalan modüller sonunda biraz daha yaşanabilir hale geldi.",
    images: [
      img("lm1", "photo-1516321497487-e288fb19713f", "Çalışma masası ve laptop"),
      img("lm1b", "photo-1498050108023-c5249f4df085", "Laptop ve notlar"),
      img("lm1c", "photo-1518770660439-4636190af475", "Elektronik kart yakın plan"),
    ],
    content: [{ type: "paragraph", text: "Bugün teknik olarak çok büyük bir şey yapmadım ama çalışma alanını toparlamak kafayı da toparlıyor." }],
    publishedAt: "2026-05-12",
    location: "İstanbul",
    tags: [{ id: "lt1", name: "günlük", slug: "gunluk" }, { id: "lt2", name: "hobi", slug: "hobi" }],
    visibility: "public",
  },
  {
    id: "l2",
    title: "Akşam yürüyüşü ve biraz müzik",
    slug: "aksam-yuruyusu-ve-biraz-muzik",
    excerpt: "Uzun bir çalışma gününden sonra kulaklık, sakin bir rota ve kafayı boşaltan birkaç şarkı.",
    images: [img("lm2", "photo-1500530855697-b586d89ba3ee", "Gün batımında yürüyüş yolu")],
    content: [
      { type: "paragraph", text: "Bazen en iyi fikirler bilgisayar başında değil, dışarıda yürürken geliyor. Bugün de biraz müzik, biraz sessizlik ve temiz hava iyi geldi." },
      { type: "callout", tone: "info", title: "O günün hissi", text: "Teknik işleri bir süre kenara bırakıp sadece yürümek ve dinlemek." },
    ],
    publishedAt: "2026-05-19",
    location: "İstanbul",
    tags: [{ id: "lt3", name: "müzik", slug: "muzik" }, { id: "lt4", name: "yürüyüş", slug: "yuruyus" }],
    visibility: "public",
  },
  {
    id: "l3",
    title: "Arkadaşlarla kısa bir mola",
    slug: "arkadaslarla-kisa-bir-mola",
    excerpt: "Projeler, sınavlar ve yapılacaklar arasında kısa ama iyi gelen bir buluşma.",
    images: [img("lm3", "photo-1529156069898-49953e39b3ac", "Arkadaşlarla dışarıda buluşma")],
    content: [
      { type: "paragraph", text: "Her şeyi verimli yapmak zorunda değiliz. Bazen iki saatlik sohbet bile haftanın geri kalanını toparlıyor." },
    ],
    publishedAt: "2026-05-08",
    location: "İstanbul",
    tags: [{ id: "lt5", name: "arkadaşlar", slug: "arkadaslar" }, { id: "lt1", name: "günlük", slug: "gunluk" }],
    visibility: "public",
  },
  {
    id: "l4",
    title: "Haftalık spor notu",
    slug: "haftalik-spor-notu",
    excerpt: "Düzenli kalmaya çalıştığım, küçük hedeflerle ilerleyen spor rutini.",
    images: [img("lm4", "photo-1517836357463-d25dfeac3438", "Spor salonu ekipmanları")],
    content: [
      { type: "paragraph", text: "Spor tarafında hedefim büyük iddialar değil, ritmi kaybetmemek. Haftaya daha dengeli başlamak için iyi bir reset oluyor." },
    ],
    publishedAt: "2026-04-28",
    tags: [{ id: "lt6", name: "spor", slug: "spor" }, { id: "lt7", name: "rutin", slug: "rutin" }],
    visibility: "public",
  },
];

export const notes: Note[] = [
  { id: "n1", slug: "uart-dma-notu", content: "UART DMA receive tarafında idle line yakalamayı parser'dan ayırınca kod çok daha rahat okunuyor.", createdAt: "2026-05-18", tags: [tags[0], tags[1], tags[2]] },
  { id: "n2", slug: "freertos-queue-notu", content: "FreeRTOS queue kullanırken mesaj boyutu küçük ve sabit kaldığında debug etmek belirgin şekilde kolaylaşıyor.", createdAt: "2026-05-15", tags: [tags[4]] },
  { id: "n3", slug: "ble-payload-notu", content: "BLE advertisement parser'da ilk kontrol her zaman field length sınırı olmalı.", createdAt: "2026-05-01", tags: [tags[3]] },
];
